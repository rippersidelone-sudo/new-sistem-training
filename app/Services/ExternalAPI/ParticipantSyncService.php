<?php

namespace App\Services\ExternalAPI;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Carbon\Carbon;

class ParticipantSyncService
{
    public function __construct(private ApiClient $client)
    {
    }

    /**
     * Sync semua teachers dari API sebagai Participant
     *
     * Logika sync:
     * - Data belum ada di lokal  → CREATE
     * - Data ada + updated_at API > last_synced_at lokal → UPDATE
     * - Data ada + updated_at API == last_synced_at lokal → SKIP
     * - Data ada + API tidak kirim updated_at → bandingkan field
     */
    public function sync(?int $onlyLocalBranchId = null): array
    {
        $response = $this->client->getTeachers();

        if (!($response['success'] ?? false)) {
            return [
                'success'       => false,
                'created'       => 0,
                'updated'       => 0,
                'skipped'       => 0,
                'errors'        => 0,
                'error_details' => [],
                'message'       => $response['message'] ?? 'Gagal mengambil data participants dari API.',
            ];
        }

        $apiParticipants = $response['data'] ?? [];

        // ── DEBUG: log raw response agar mudah diagnosa ──────────────────
        Log::info('ParticipantSyncService: raw API response', [
            'total'  => count($apiParticipants),
            'sample' => array_slice($apiParticipants, 0, 3),
        ]);

        if (empty($apiParticipants)) {
            Log::warning('ParticipantSyncService: API mengembalikan data kosong', [
                'response' => $response,
            ]);

            return [
                'success'       => true,
                'created'       => 0,
                'updated'       => 0,
                'skipped'       => 0,
                'errors'        => 0,
                'error_details' => [],
                'message'       => 'Tidak ada data participants dari API. Cek endpoint atau API Key.',
            ];
        }

        // Role ID untuk Participant — pastikan sesuai di DB
        $participantRoleId = (int)(config('external_api.participant_role_id') ?? 5);
        if ($participantRoleId <= 0) $participantRoleId = 5;

        $created      = 0;
        $updated      = 0;
        $skipped      = 0;
        $errors       = 0;
        $errorDetails = [];

        foreach ($apiParticipants as $apiParticipant) {
            try {
                // Filter per branch jika diminta (untuk Branch Coordinator)
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
                $teacherId      = $apiParticipant['teacher_id'] ?? 'unknown';
                $name           = $apiParticipant['name'] ?? 'unknown';
                $dbError        = $this->parseQueryException($e);
                $errorDetails[] = "[ID: {$teacherId}] {$name} → {$dbError}";

                Log::error('ParticipantSyncService: DB error', [
                    'teacher_id' => $teacherId,
                    'email'      => $apiParticipant['email'] ?? null,
                    'error'      => $e->getMessage(),
                ]);

            } catch (\Throwable $e) {
                $errors++;
                $teacherId      = $apiParticipant['teacher_id'] ?? 'unknown';
                $name           = $apiParticipant['name'] ?? 'unknown';
                $errorDetails[] = "[ID: {$teacherId}] {$name} → {$e->getMessage()}";

                Log::error('ParticipantSyncService: Error', [
                    'teacher_id' => $teacherId,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        $total = $created + $updated + $skipped;

        // ── success = true selama API berhasil dihubungi ─────────────────
        // Error per-record (duplikat, constraint) tidak membuat sync "gagal"
        return [
            'success'       => true,
            'created'       => $created,
            'updated'       => $updated,
            'skipped'       => $skipped,
            'errors'        => $errors,
            'error_details' => $errorDetails,
            'message'       => "Sync selesai! Total: {$total}. Baru: {$created}, Update: {$updated}, Skip: {$skipped}."
                             . ($errors > 0 ? " Gagal: {$errors}." : ''),
        ];
    }

    // ============================================================
    // CORE: sync satu participant
    // ============================================================

    private function syncSingleParticipant(array $apiParticipant, int $participantRoleId): string
    {
        $externalId = $apiParticipant['teacher_id'] ?? null;

        if (!$externalId) {
            throw new \RuntimeException('teacher_id kosong dari API');
        }

        // Cek apakah user sudah ada (termasuk soft deleted)
        $localUser = User::withTrashed()->where('external_id', $externalId)->first();

        if (!$localUser) {
            $this->createParticipant($apiParticipant, $participantRoleId);
            return 'created';
        }

        // Restore jika soft deleted
        if ($localUser->trashed()) {
            $localUser->restore();
        }

        return $this->updateOrSkip($localUser, $apiParticipant);
    }

    // ============================================================
    // LOGIKA: update vs skip berdasarkan updated_at API
    // ============================================================

    /**
     * Bandingkan updated_at API dengan last_synced_at lokal.
     * Jika API lebih baru → update. Jika sama/lebih lama → skip.
     */
    private function updateOrSkip(User $localUser, array $apiParticipant): string
    {
        $apiUpdatedAt  = $this->parseTimestamp($apiParticipant['updated_at'] ?? null);
        $localSyncedAt = $localUser->last_synced_at;

        if ($apiUpdatedAt !== null && $localSyncedAt !== null) {
            // Punya timestamp dari kedua sisi → bandingkan langsung
            if (!$apiUpdatedAt->gt($localSyncedAt)) {
                // Data di API tidak lebih baru → SKIP (touch last_synced_at saja)
                $localUser->timestamps = false;
                $localUser->update(['last_synced_at' => now()]);
                $localUser->timestamps = true;
                return 'skipped';
            }
        } else {
            // Fallback: bandingkan field satu per satu
            if (!$this->hasFieldChanges($localUser, $apiParticipant)) {
                $localUser->timestamps = false;
                $localUser->update(['last_synced_at' => now()]);
                $localUser->timestamps = true;
                return 'skipped';
            }
        }

        $this->performUpdate($localUser, $apiParticipant);
        return 'updated';
    }

    private function performUpdate(User $localUser, array $apiParticipant): void
    {
        $newBranchId     = $this->resolveBranchId($apiParticipant['branches'] ?? []);
        $apiPasswordHash = $apiParticipant['password_hash'] ?? null;
        $email           = $this->normalizeEmail(
            $apiParticipant['email'] ?? '',
            $apiParticipant['teacher_id'] ?? null,
            $localUser->id
        );

        $updateData = [
            'name'           => $apiParticipant['name'] ?? $localUser->name,
            'email'          => $email,
            'phone'          => $apiParticipant['phone'] ?? null,
            'username'       => $apiParticipant['username'] ?? $localUser->username,
            'branch_id'      => $newBranchId,
            'last_synced_at' => now(),
        ];

        if ($apiPasswordHash && $apiPasswordHash !== $localUser->password) {
            $updateData['password'] = $apiPasswordHash;
        }

        $localUser->update($updateData);
    }

    private function hasFieldChanges(User $localUser, array $apiParticipant): bool
    {
        $email       = $this->normalizeEmail(
            $apiParticipant['email'] ?? '',
            $apiParticipant['teacher_id'] ?? null,
            $localUser->id
        );
        $newBranchId = $this->resolveBranchId($apiParticipant['branches'] ?? []);

        return
            $localUser->name      !== ($apiParticipant['name'] ?? null) ||
            $localUser->email     !== $email ||
            $localUser->username  !== ($apiParticipant['username'] ?? null) ||
            ($localUser->phone ?? null) !== ($apiParticipant['phone'] ?? null) ||
            $localUser->branch_id !== $newBranchId;
    }

    private function createParticipant(array $apiParticipant, int $participantRoleId): User
    {
        $externalId = $apiParticipant['teacher_id'] ?? null;

        if (!$externalId) {
            throw new \RuntimeException('teacher_id kosong saat create');
        }

        $branchId        = $this->resolveBranchId($apiParticipant['branches'] ?? []);
        $email           = $this->normalizeEmail($apiParticipant['email'] ?? '', $externalId, null);
        $apiPasswordHash = $apiParticipant['password_hash'] ?? null;
        $password        = $apiPasswordHash ?: Hash::make('password123');

        return User::create([
            'external_id'    => $externalId,
            'role_id'        => $participantRoleId,
            'branch_id'      => $branchId,
            'name'           => $apiParticipant['name'] ?? '',
            'username'       => $apiParticipant['username'] ?? 'participant-' . $externalId,
            'email'          => $email,
            'phone'          => $apiParticipant['phone'] ?? null,
            'password'       => $password,
            'last_synced_at' => now(),
        ]);
    }

    // ============================================================
    // HELPERS
    // ============================================================

    private function parseTimestamp(?string $value): ?Carbon
    {
        if (empty($value)) return null;
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Resolve branch_id lokal dari array branches API.
     *
     * API bisa mengembalikan berbagai format:
     * - [{'branch_id': 1, ...}]
     * - [1, 2, 3]
     * - [{'id': 1}]
     */
    private function resolveBranchId(array $branches): ?int
    {
        if (empty($branches)) return null;

        $first = $branches[0];

        // Bentuk array of object
        if (is_array($first)) {
            $externalBranchId = $first['branch_id'] ?? $first['id'] ?? null;
        } else {
            // Bentuk array of scalar (int/string)
            $externalBranchId = $first;
        }

        if (!$externalBranchId) return null;

        $branchId = Branch::where('external_id', $externalBranchId)->value('id');

        // ── DEBUG: log jika branch tidak ditemukan ───────────────────────
        if (!$branchId) {
            Log::warning('ParticipantSyncService: branch tidak ditemukan di lokal', [
                'external_branch_id' => $externalBranchId,
                'hint'               => 'Jalankan sync branches terlebih dahulu',
            ]);
        }

        return $branchId;
    }

    private function normalizeEmail(?string $email, $externalTeacherId = null, ?int $ignoreUserId = null): string
    {
        $email = trim((string) $email);

        if ($email === '') {
            $email = 'teacher-' . ($externalTeacherId ?? uniqid()) . '@external.local';
        }

        $candidate = $email;
        $i         = 0;

        while (true) {
            $q = User::withTrashed()->where('email', $candidate);

            if ($ignoreUserId) {
                $q->where('id', '!=', $ignoreUserId);
            }

            if (!$q->exists()) {
                return $candidate;
            }

            $i++;
            [$local, $domain] = array_pad(explode('@', $email, 2), 2, 'external.local');
            $candidate        = $local . '+' . $i . '@' . $domain;
        }
    }

    private function parseQueryException(QueryException $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, '1062') || str_contains($message, 'Duplicate entry')) {
            if (str_contains($message, 'email'))       return 'Email sudah terdaftar';
            if (str_contains($message, 'username'))    return 'Username sudah terdaftar';
            if (str_contains($message, 'external_id')) return 'External ID duplikat';
            return 'Data duplikat';
        }
        if (str_contains($message, '1048') || str_contains($message, 'cannot be null')) {
            return 'Ada field wajib yang kosong dari API';
        }
        if (str_contains($message, '1452') || str_contains($message, 'foreign key constraint')) {
            return 'Branch atau Role tidak ditemukan di sistem';
        }

        return substr($message, 0, 150);
    }
}