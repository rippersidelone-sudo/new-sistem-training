<?php

namespace App\Http\Controllers;

use App\Services\ExternalAPI\BranchSyncService;
use App\Services\ExternalAPI\ParticipantSyncService;
use App\Services\ExternalAPI\CategorySyncService;
use App\Services\ExternalAPI\ApiClient;
use App\Helpers\RoleHelper;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function __construct(
        private BranchSyncService      $branchSync,
        private ParticipantSyncService $participantSync,
        private CategorySyncService    $categorySync,
        private ApiClient              $apiClient,
    ) {
    }

    // ============================================================
    // SYNC ACTIONS
    // ============================================================

    /**
     * Sync semua data sekaligus
     * HQ Admin & Coordinator: Branches + Participants + Categories
     * Branch Coordinator: Participants saja (scope cabang-nya)
     */
    public function syncAll()
    {
        $user     = auth()->user();
        $results  = [];
        $hasError = false;

        if (RoleHelper::isBranchCoordinator($user)) {
            // Branch Coordinator hanya sync participants
            $result = $this->participantSync->sync();
            $results['participants'] = $result;
            if (!$result['success']) $hasError = true;

        } else {
            // HQ Admin & Coordinator sync semua
            // 1. Branches dulu (participants butuh branch_id)
            $branchResult = $this->branchSync->sync();
            $results['branches'] = $branchResult;
            if (!$branchResult['success']) $hasError = true;

            // 2. Categories
            $categoryResult = $this->categorySync->sync();
            $results['categories'] = $categoryResult;
            if (!$categoryResult['success']) $hasError = true;

            // 3. Participants terakhir
            $participantResult = $this->participantSync->sync();
            $results['participants'] = $participantResult;
            if (!$participantResult['success']) $hasError = true;
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => !$hasError,
                'results' => $results,
                'message' => $this->buildSummaryMessage($results),
            ]);
        }

        return redirect()->back()->with(
            $hasError ? 'error' : 'success',
            $this->buildSummaryMessage($results)
        );
    }

    /**
     * Sync branches saja
     * Hanya HQ Admin & Coordinator
     */
    public function syncBranches()
    {
        $result = $this->branchSync->sync();

        if (request()->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Sync participants saja
     * HQ Admin, Coordinator, Branch Coordinator
     */
    public function syncParticipants()
    {
        $user = auth()->user();

        if (\App\Helpers\RoleHelper::isBranchCoordinator($user)) {
            // sync khusus cabang user ini
            $result = $this->participantSync->sync($user->branch_id);
        } else {
            // HQ/Coordinator: sync semua
            $result = $this->participantSync->sync();
        }

        if (request()->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Sync categories saja
     * Hanya HQ Admin & Coordinator
     */
    public function syncCategories()
    {
        $result = $this->categorySync->sync();

        if (request()->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    /**
     * Test koneksi ke API
     */
    public function testConnection()
    {
        $result = $this->apiClient->testConnection();

        if (request()->ajax()) {
            return response()->json($result);
        }

        return redirect()->back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    // ============================================================
    // HELPER
    // ============================================================

    /**
     * Build summary message dari semua hasil sync
     */
    private function buildSummaryMessage(array $results): string
    {
        $parts = [];

        $format = function (string $label, array $r): string {
            $created  = (int)($r['created']  ?? 0);
            $updated  = (int)($r['updated']  ?? 0);
            $restored = (int)($r['restored'] ?? 0);
            $skipped  = (int)($r['skipped']  ?? 0);
            $errors   = (int)($r['errors']   ?? 0);

            $text = "{$label}: {$created} baru";

            if ($restored > 0) {
                $text .= ", {$restored} dipulihkan";
            }

            $text .= ", {$updated} diperbarui, {$skipped} sama";

            if ($errors > 0) {
                $text .= ", {$errors} gagal";
            }

            return $text;
        };

        if (isset($results['branches'])) {
            $parts[] = $format('Branches', $results['branches']);
        }

        if (isset($results['categories'])) {
            $parts[] = $format('Categories', $results['categories']);
        }

        if (isset($results['participants'])) {
            $parts[] = $format('Participants', $results['participants']);
        }

        return 'Sync selesai! ' . implode(' | ', $parts);
    }

}