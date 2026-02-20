<?php

namespace App\Services\ExternalAPI;

use App\Models\Category;
use Illuminate\Support\Facades\Log;

class CategorySyncService
{
    public function __construct(private ApiClient $client)
    {
    }

    /**
     * Sync semua skills dari API sebagai Category
     * Logic:
     * - UPDATE jika sudah ada (termasuk yang soft-deleted)
     * - CREATE jika belum ada
     * - SKIP jika tidak ada perubahan pada field yang disync
     * - Category manual (external_id NULL) tidak tersentuh
     *
     * @return array { success, created, updated, skipped, errors, message }
     */
    public function sync(): array
    {
        $response = $this->client->getSkills();

        if (!($response['success'] ?? false)) {
            return [
                'success' => false,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors'  => 0,
                'message' => $response['message'] ?? 'Gagal mengambil data categories dari API.',
            ];
        }

        $apiSkills = $response['data'] ?? [];

        if (empty($apiSkills)) {
            return [
                'success' => true,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors'  => 0,
                'message' => 'Tidak ada data categories dari API.',
            ];
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors  = 0;

        foreach ($apiSkills as $apiSkill) {
            try {
                // Wajib ada skill_id sebagai external_id
                if (!isset($apiSkill['skill_id'])) {
                    $skipped++;
                    continue;
                }

                $result = $this->syncSingleCategory($apiSkill);

                match ($result) {
                    'created' => $created++,
                    'updated' => $updated++,
                    'skipped' => $skipped++,
                    default   => $skipped++,
                };

            } catch (\Throwable $e) {
                $errors++;
                Log::error('CategorySyncService: Error syncing category', [
                    'skill_id' => $apiSkill['skill_id'] ?? null,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        $total = $created + $updated + $skipped;

        return [
            'success' => true,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors'  => $errors,
            'message' => "Sync selesai! Total: {$total} categories. Baru: {$created}, Diperbarui: {$updated}, Sama: {$skipped}." .
                         ($errors > 0 ? " Gagal: {$errors}." : ''),
        ];
    }

    /**
     * Sync satu category dari API
     * Return: 'created' | 'updated' | 'skipped'
     */
    private function syncSingleCategory(array $apiSkill): string
    {
        $externalId = $apiSkill['skill_id'];

        // Data yang disync dari API -> lokal
        $incoming = $this->mapApiToLocalPayload($apiSkill);

        // Cari existing termasuk yang soft-deleted
        $local = Category::withTrashed()
            ->where('external_id', $externalId)
            ->first();

        if (!$local) {
            // Create baru (WAJIB amanin name biar tidak bentrok unique)
            $incoming['name'] = $this->safeNameForCreate($incoming['name'] ?? '');
            $this->createCategory($externalId, $incoming);
            return 'created';
        }

        // Update / skip / restore
        return $this->updateOrSkipCategory($local, $incoming);
    }

    /**
     * Mapping data API skill -> payload Category lokal
     */
    private function mapApiToLocalPayload(array $apiSkill): array
    {
        return [
            'name'        => trim($apiSkill['skill_name'] ?? ''),
            'description' => null,
        ];
    }

    /**
     * Create category baru dari API
     */
    private function createCategory(int|string $externalId, array $incoming): Category
    {
        return Category::create([
            'external_id'    => $externalId,
            'name'           => $incoming['name'] ?? '',
            'description'    => $incoming['description'] ?? null,
            'last_synced_at' => now(),
        ]);
    }

    /**
     * Update category jika ada perubahan.
     * - Kalau soft-deleted -> restore
     * - Kalau field sama -> skip
     * - Kalau beda -> update
     */
    private function updateOrSkipCategory(Category $local, array $incoming): string
    {
        $isSoftDeletes = method_exists($local, 'trashed');
        $wasTrashed    = $isSoftDeletes && $local->trashed();

        // Restore dulu kalau trashed
        if ($wasTrashed) {
            $local->restore();
        }

        // ==== PENTING: amankan NAME kalau mau berubah ====
        if (array_key_exists('name', $incoming)) {
            $incoming['name'] = $this->safeNameForUpdate(
                newName: (string)$incoming['name'],
                currentCategoryId: $local->id
            );
        }

        // Field yang kita bandingkan untuk menentukan "berubah atau tidak"
        $fieldsToCompare = [
            'name',
            'description',
        ];

        $hasChanges = false;
        foreach ($fieldsToCompare as $field) {
            $localValue    = $local->{$field};
            $incomingValue = $incoming[$field] ?? null;

            if ($localValue !== $incomingValue) {
                $hasChanges = true;
                break;
            }
        }

        // Kalau tidak ada perubahan field & tidak restore => skip
        if (!$hasChanges && !$wasTrashed) {
            $local->update([
                'last_synced_at' => now(),
            ]);
            return 'skipped';
        }

        // Update payload: selalu update last_synced_at
        $payload = array_merge($incoming, [
            'last_synced_at' => now(),
            'deleted_at'     => null,
        ]);

        $local->update($payload);

        return 'updated';
    }

    // ============================================================
    // NEW: NAME CONFLICT HANDLING (karena categories.name UNIQUE)
    // ============================================================

    /**
     * Untuk CREATE: kalau name sudah ada (termasuk soft deleted) -> bikin unik
     */
    private function safeNameForCreate(string $name): string
    {
        $name = trim($name);
        if ($name === '') return 'Untitled Category';

        return $this->makeUniqueName($name, null);
    }

    /**
     * Untuk UPDATE: kalau name tidak berubah -> biarkan.
     * Kalau berubah tapi bentrok -> bikin unik.
     */
    private function safeNameForUpdate(string $newName, int $currentCategoryId): string
    {
        $newName = trim($newName);
        if ($newName === '') return 'Untitled Category';

        // kalau nama yang sama persis, tidak perlu dicek
        $current = Category::withTrashed()->find($currentCategoryId);
        if ($current && $current->name === $newName) {
            return $newName;
        }

        // kalau ada category lain pakai name ini -> bikin unik
        return $this->makeUniqueName($newName, $currentCategoryId);
    }

    /**
     * Bikin name unik dengan pola:
     * "Python Game", kalau bentrok -> "Python Game (1)", "Python Game (2)", dst.
     *
     * $ignoreId = id yang sedang diupdate (boleh null kalau create).
     */
    private function makeUniqueName(string $baseName, ?int $ignoreId = null): string
    {
        $baseName = trim($baseName);
        $i = 0;

        while (true) {
            $candidate = $i === 0 ? $baseName : "{$baseName} ({$i})";

            $query = Category::withTrashed()->where('name', $candidate);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $candidate;
            }

            $i++;
        }
    }
}
