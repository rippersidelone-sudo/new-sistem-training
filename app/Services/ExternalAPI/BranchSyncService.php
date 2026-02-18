<?php

namespace App\Services\ExternalAPI;

use App\Models\Branch;
use Illuminate\Support\Facades\Log;

class BranchSyncService
{
    public function __construct(private ApiClient $client)
    {
    }

    /**
     * Sync semua branches dari API
     * Logic:
     * - jika external_id ada di DB (termasuk soft deleted) => restore jika trashed, lalu update jika perlu
     * - jika belum ada => create baru
     *
     * @return array { success, created, updated, restored, skipped, errors, message }
     */
    public function sync(): array
    {
        $response = $this->client->getBranches();

        if (!$response['success']) {
            return [
                'success'  => false,
                'created'  => 0,
                'updated'  => 0,
                'restored' => 0,
                'skipped'  => 0,
                'errors'   => 0,
                'message'  => $response['message'] ?? 'Gagal mengambil data branches dari API.',
            ];
        }

        $apiBranches = $response['data'] ?? [];

        if (empty($apiBranches)) {
            return [
                'success'  => true,
                'created'  => 0,
                'updated'  => 0,
                'restored' => 0,
                'skipped'  => 0,
                'errors'   => 0,
                'message'  => 'Tidak ada data branches dari API.',
            ];
        }

        $created = 0;
        $updated = 0;
        $restored = 0;
        $skipped = 0;
        $errors  = 0;

        foreach ($apiBranches as $apiBranch) {
            try {
                $result = $this->syncSingleBranch($apiBranch);

                match ($result) {
                    'created'  => $created++,
                    'updated'  => $updated++,
                    'restored' => $restored++,
                    'skipped'  => $skipped++,
                };
            } catch (\Throwable $e) {
                $errors++;
                Log::error('BranchSyncService: Error syncing branch', [
                    'branch_id' => $apiBranch['branch_id'] ?? null,
                    'payload'   => $apiBranch,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        $total = $created + $updated + $restored + $skipped;

        return [
            'success'  => true,
            'created'  => $created,
            'updated'  => $updated,
            'restored' => $restored,
            'skipped'  => $skipped,
            'errors'   => $errors,
            'message'  => "Sync selesai! Total: {$total} branches. Baru: {$created}, Dipulihkan: {$restored}, Diperbarui: {$updated}, Sama: {$skipped}."
                        . ($errors > 0 ? " Gagal: {$errors}." : ''),
        ];
    }

    /**
     * Sync satu branch
     * Return: 'created' | 'updated' | 'restored' | 'skipped'
     */
    private function syncSingleBranch(array $apiBranch): string
    {
        $externalId = $apiBranch['branch_id'] ?? null;

        if (!$externalId) {
            throw new \RuntimeException('branch_id kosong dari API');
        }

        $newData = $this->mapApiData($apiBranch);

        // ✅ penting: include soft deleted
        $localBranch = Branch::withTrashed()
            ->where('external_id', $externalId)
            ->first();

        // Jika ada branch (termasuk trashed)
        if ($localBranch) {

            // ✅ restore dulu kalau trashed
            $wasTrashed = $localBranch->trashed();
            if ($wasTrashed) {
                $localBranch->restore();
            }

            // Cek perubahan data
            $hasChanges =
                $localBranch->name    !== $newData['name'] ||
                $localBranch->address !== $newData['address'] ||
                $localBranch->contact !== $newData['contact'] ||
                $localBranch->code    !== $newData['code'];

            // Kalau trashed dan direstore, biasanya kita anggap ada perubahan penting (dipulihkan)
            // Tapi kalau kamu ingin dia juga update field terbaru, tetap update.
            if ($wasTrashed) {
                $localBranch->update(array_merge($newData, [
                    'last_synced_at' => now(),
                ]));
                return 'restored';
            }

            if (!$hasChanges) {
                // Optional: kalau mau tetap update last_synced_at meskipun sama, buka comment di bawah
                // $localBranch->update(['last_synced_at' => now()]);
                return 'skipped';
            }

            $localBranch->update(array_merge($newData, [
                'last_synced_at' => now(),
            ]));

            return 'updated';
        }

        // Tidak ada di lokal => create baru
        Branch::create(array_merge($newData, [
            'external_id'    => $externalId,
            'last_synced_at' => now(),
        ]));

        return 'created';
    }

    /**
     * Map data dari API ke format database lokal
     */
    private function mapApiData(array $apiBranch): array
    {
        return [
            'code'    => $apiBranch['branch_code'] ?? null,
            'name'    => $apiBranch['branch_name'] ?? '',
            'address' => $apiBranch['address'] ?? null,
            'contact' => $apiBranch['contact'] ?? null,
        ];
    }
}
