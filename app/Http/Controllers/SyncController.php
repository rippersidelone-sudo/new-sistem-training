<?php

namespace App\Http\Controllers;

use App\Services\ExternalAPI\BranchSyncService;
use App\Services\ExternalAPI\ParticipantSyncService;
use App\Services\ExternalAPI\CategorySyncService;
use App\Services\ExternalAPI\ApiClient;
use App\Helpers\RoleHelper;

class SyncController extends Controller
{
    public function __construct(
        private BranchSyncService      $branchSync,
        private ParticipantSyncService $participantSync,
        private CategorySyncService    $categorySync,
        private ApiClient              $apiClient,
    ) {}

    public function syncAll()
    {
        $user    = auth()->user();
        $results = [];

        if (RoleHelper::isBranchCoordinator($user)) {
            $results['participants'] = $this->participantSync->sync($user->branch_id);
        } else {
            $results['branches']     = $this->branchSync->sync();
            $results['categories']   = $this->categorySync->sync();
            $results['participants'] = $this->participantSync->sync();
        }

        // ── Hanya merah jika API benar-benar tidak bisa dihubungi ────────
        // Error per-record (duplikat, constraint) = warning kuning, bukan merah
        $hasApiFailure  = collect($results)->contains(fn($r) => !($r['success'] ?? true));
        $hasRecordError = collect($results)->contains(fn($r) => ($r['errors'] ?? 0) > 0);

        $summaryMessage = $this->buildSummaryMessage($results);
        $errorDetails   = $this->collectErrorDetails($results);

        if (request()->ajax()) {
            return response()->json([
                'success'       => !$hasApiFailure,
                'results'       => $results,
                'message'       => $summaryMessage,
                'error_details' => $errorDetails,
            ]);
        }

        if ($hasApiFailure) {
            session()->flash('error', $summaryMessage);
        } elseif ($hasRecordError) {
            // API OK tapi ada record yang gagal (duplikat dll) → warning
            session()->flash('warning', $summaryMessage);
            session()->flash('error_details', $errorDetails);
        } else {
            session()->flash('success', $summaryMessage);
        }

        return redirect()->back();
    }

    public function syncBranches()
    {
        $result = $this->branchSync->sync();

        if (request()->ajax()) return response()->json($result);

        return redirect()->back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function syncParticipants()
    {
        $user   = auth()->user();
        $result = RoleHelper::isBranchCoordinator($user)
            ? $this->participantSync->sync($user->branch_id)
            : $this->participantSync->sync();

        if (request()->ajax()) return response()->json($result);

        $hasErrors = ($result['errors'] ?? 0) > 0;

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }
        if ($hasErrors) {
            session()->flash('warning', $result['message']);
            session()->flash('error_details', $result['error_details'] ?? []);
            return redirect()->back();
        }

        return redirect()->back()->with('success', $result['message']);
    }

    public function syncCategories()
    {
        $result = $this->categorySync->sync();

        if (request()->ajax()) return response()->json($result);

        return redirect()->back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function testConnection()
    {
        $result = $this->apiClient->testConnection();

        if (request()->ajax()) return response()->json($result);

        return redirect()->back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    // ============================================================
    // HELPERS
    // ============================================================

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
            if ($restored > 0) $text .= ", {$restored} dipulihkan";
            $text .= ", {$updated} diperbarui, {$skipped} sama";
            if ($errors > 0)   $text .= ", {$errors} gagal";
            return $text;
        };

        foreach (['branches' => 'Branches', 'categories' => 'Categories', 'participants' => 'Participants'] as $key => $label) {
            if (isset($results[$key])) {
                $parts[] = $format($label, $results[$key]);
            }
        }

        return 'Sync selesai! ' . implode(' | ', $parts);
    }

    private function collectErrorDetails(array $results): array
    {
        $all = [];
        foreach ($results as $key => $result) {
            foreach ($result['error_details'] ?? [] as $detail) {
                $all[] = '[' . ucfirst($key) . '] ' . $detail;
            }
        }
        return $all;
    }
}