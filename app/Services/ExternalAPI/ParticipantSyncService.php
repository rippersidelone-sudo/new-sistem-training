<?php

namespace App\Services\ExternalAPI;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class ParticipantSyncService
{
    public function __construct(private ApiClient $client)
    {
    }

    /**
     * Sync semua teachers dari API sebagai Participant
     * Bisa dibatasi untuk cabang tertentu (khusus Branch PIC).
     *
     * @param int|null $onlyLocalBranchId  branch_id lokal (users.branch_id / branches.id)
     * @return array { success, created, updated, skipped, errors, message }
     */
    public function sync(?int $onlyLocalBranchId = null): array
    {
        $response = $this->client->getTeachers();

        if (!($response['success'] ?? false)) {
            return [
                'success' => false,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors'  => 0,
                'message' => $response['message'] ?? 'Gagal mengambil data participants dari API.',
            ];
        }

        $apiParticipants = $response['data'] ?? [];

        if (empty($apiParticipants)) {
            return [
                'success' => true,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors'  => 0,
                'message' => 'Tidak ada data participants dari API.',
            ];
        }

        $participantRoleId = (int) (config('external_api.participant_role_id') ?? 5); // fallback 5
        if ($participantRoleId <= 0) {
            // biar gak NULL
            $participantRoleId = 5;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors  = 0;

        foreach ($apiParticipants as $apiParticipant) {
            try {
                // kalau Branch PIC: filter hanya peserta untuk cabang dia
                if ($onlyLocalBranchId !== null) {
                    $resolvedLocalBranchId = $this->resolveBranchId($apiParticipant['branches'] ?? []);
                    if ($resolvedLocalBranchId !== $onlyLocalBranchId) {
                        $skipped++;
                        continue;
                    }
                }

                $result = $this->syncSingleParticipant($apiParticipant, $participantRoleId);

                match ($result) {
                    'created' => $created++,
                    'updated' => $updated++,
                    'skipped' => $skipped++,
                    default   => $skipped++,
                };

            } catch (QueryException $e) {
                $errors++;
                Log::error('ParticipantSyncService: DB error syncing participant', [
                    'teacher_id' => $apiParticipant['teacher_id'] ?? null,
                    'email'      => $apiParticipant['email'] ?? null,
                    'sql'        => $e->getSql(),
                    'bindings'   => $e->getBindings(),
                    'error'      => $e->getMessage(),
                ]);
            } catch (\Throwable $e) {
                $errors++;
                Log::error('ParticipantSyncService: Error syncing participant', [
                    'teacher_id' => $apiParticipant['teacher_id'] ?? null,
                    'email'      => $apiParticipant['email'] ?? null,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        $total = $created + $updated + $skipped;

        return [
            'success' => ($errors === 0), // kalau ada error, biar frontend tau gagal sebagian
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors'  => $errors,
            'message' => "Sync selesai! Total diproses: {$total}. Baru: {$created}, Update: {$updated}, Skip: {$skipped}." .
                         ($errors > 0 ? " Error: {$errors}. Cek laravel.log" : ''),
        ];
    }

    /**
     * Sync satu participant
     * Return: 'created' | 'updated' | 'skipped'
     */
    private function syncSingleParticipant(array $apiParticipant, int $participantRoleId): string
    {
        $externalId = $apiParticipant['teacher_id'] ?? null;

        if (!$externalId) {
            // data rusak
            throw new \RuntimeException('teacher_id kosong dari API');
        }

        $localUser = User::where('external_id', $externalId)->first();

        if ($localUser) {
            return $this->updateParticipant($localUser, $apiParticipant);
        }

        $this->createParticipant($apiParticipant, $participantRoleId);
        return 'created';
    }

    /**
     * Update participant jika ada perubahan
     * Return: 'updated' | 'skipped'
     */
    private function updateParticipant(User $localUser, array $apiParticipant): string
    {
        $newData = $this->mapApiData($apiParticipant);

        // ✅ normalize email agar tidak bikin unique error karena null/empty
        $newData['email'] = $this->normalizeEmail($newData['email'], $apiParticipant['teacher_id'] ?? null, $localUser->id);

        $newBranchId = $this->resolveBranchId($apiParticipant['branches'] ?? []);

        $hasDataChanges =
            $localUser->name  !== $newData['name'] ||
            $localUser->email !== $newData['email'] ||
            ($localUser->phone ?? null) !== ($newData['phone'] ?? null);

        $hasBranchChange = ($localUser->branch_id !== $newBranchId);

        // ⚠️ password_hash dari API: belum tentu bcrypt kompatibel dengan Laravel.
        // Jadi untuk participant, lebih aman TIDAK overwrite password lokal otomatis.
        // Kalau kamu tetap mau, aktifkan flag di env/config.
        $allowSyncPassword = (bool) (config('external_api.sync_participant_password') ?? false);

        $apiPasswordHash = $apiParticipant['password_hash'] ?? null;
        $hasPasswordChange = $allowSyncPassword && $apiPasswordHash && ($apiPasswordHash !== $localUser->password);

        if (!$hasDataChanges && !$hasBranchChange && !$hasPasswordChange) {
            // tetap update last_synced_at biar kelihatan udah sync
            $localUser->update(['last_synced_at' => now()]);
            return 'skipped';
        }

        $updateData = array_merge($newData, [
            'branch_id'      => $newBranchId, // boleh null, tapi tidak bikin crash
            'last_synced_at' => now(),
        ]);

        if ($hasPasswordChange) {
            $updateData['password'] = $apiPasswordHash;
        }

        $localUser->update($updateData);

        return 'updated';
    }

    /**
     * Create participant baru
     */
    private function createParticipant(array $apiParticipant, int $participantRoleId): User
    {
        $externalId = $apiParticipant['teacher_id'] ?? null;
        if (!$externalId) {
            throw new \RuntimeException('teacher_id kosong saat create');
        }

        $branchId = $this->resolveBranchId($apiParticipant['branches'] ?? []);

        $email = $this->normalizeEmail($apiParticipant['email'] ?? '', $externalId, null);

        // password:
        // - kalau API kirim hash: JANGAN pakai untuk login Laravel kecuali memang bcrypt kompatibel
        // - default aja password123 biar bisa login testing
        $defaultPassword = Hash::make('password123');

        $allowUseApiHashOnCreate = (bool) (config('external_api.use_api_password_hash_on_create') ?? false);
        $apiPasswordHash = $apiParticipant['password_hash'] ?? null;

        return User::create([
            'external_id'    => $externalId,
            'role_id'        => $participantRoleId,
            'branch_id'      => $branchId,
            'name'           => $apiParticipant['name'] ?? '',
            'email'          => $email,
            'phone'          => $apiParticipant['phone'] ?? null,
            'password'       => ($allowUseApiHashOnCreate && $apiPasswordHash) ? $apiPasswordHash : $defaultPassword,
            'last_synced_at' => now(),
        ]);
    }

    /**
     * Map data dari API ke format database lokal
     */
    private function mapApiData(array $apiParticipant): array
    {
        return [
            'name'  => $apiParticipant['name'] ?? '',
            'email' => $apiParticipant['email'] ?? '',
            'phone' => $apiParticipant['phone'] ?? null,
        ];
    }

    /**
     * Normalize email:
     * - kalau kosong -> buat dummy "teacher-{id}@external.local"
     * - kalau bentrok unique -> tambahkan suffix
     */
    private function normalizeEmail(?string $email, $externalTeacherId = null, ?int $ignoreUserId = null): string
    {
        $email = trim((string) $email);

        if ($email === '') {
            $email = 'teacher-' . ($externalTeacherId ?? uniqid()) . '@external.local';
        }

        // Pastikan unik
        $candidate = $email;
        $i = 0;

        while (true) {
            $q = User::where('email', $candidate);
            if ($ignoreUserId) {
                $q->where('id', '!=', $ignoreUserId);
            }

            if (!$q->exists()) {
                return $candidate;
            }

            $i++;
            // email+1@domain
            $parts = explode('@', $email);
            $local = $parts[0] ?? $email;
            $domain = $parts[1] ?? 'external.local';
            $candidate = $local . '+' . $i . '@' . $domain;
        }
    }

    /**
     * Resolve branch_id dari array branches
     * Ambil branch pertama yang ada di database lokal
     *
     * API branches bisa:
     * - [1,2] (array of external_id)
     * - [{"branch_id":1}, ...]
     */
    private function resolveBranchId(array $branches): ?int
    {
        if (empty($branches)) return null;

        $first = $branches[0];

        // kalau object/array
        if (is_array($first)) {
            $firstExternalBranchId = $first['branch_id'] ?? $first['id'] ?? null;
        } else {
            $firstExternalBranchId = $first;
        }

        if (!$firstExternalBranchId) return null;

        $branch = Branch::where('external_id', $firstExternalBranchId)->first();

        return $branch?->id;
    }
}
