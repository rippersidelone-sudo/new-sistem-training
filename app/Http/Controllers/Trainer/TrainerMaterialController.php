<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\BatchMaterial;
use App\Helpers\RoleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TrainerMaterialController extends Controller
{
    // getBatchOptions
    private function getBatchOptions($trainerId): \Illuminate\Support\Collection
    {
        return Batch::where('trainer_id', $trainerId)
            ->orderByRaw("FIELD(status, 'Ongoing', 'Scheduled', 'Completed')")
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($batch) {
                $statusLabel = match($batch->status) {
                    'Ongoing'   => 'ONGOING -',
                    'Scheduled' => 'SCHEDULED -',
                    'Completed' => 'COMPLETED -',
                    default     => '',
                };
                return [
                    'value' => $batch->id,
                    'label' => $statusLabel . ' ' . $batch->title . ' — ' . formatBatchCode($batch->id),
                ];
            });
    }

    // index
    public function index(Request $request): View
    {
        $trainer = Auth::user();

        if (!RoleHelper::isTrainer($trainer)) {
            abort(403, 'Unauthorized access');
        }

        $search        = $request->input('search');
        $batchIdFilter = $request->input('batch_id');
        $typeFilter    = $request->input('type');

        $batchOptions = $this->getBatchOptions($trainer->id);

        $batches = Batch::where('trainer_id', $trainer->id)
            ->with(['category'])
            ->orderByRaw("FIELD(status, 'Ongoing', 'Scheduled', 'Completed')")
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($batch) use ($search, $batchIdFilter, $typeFilter) {
                $materialsQuery = $batch->materials();

                if ($search) {
                    $materialsQuery->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                              ->orWhere('description', 'like', "%{$search}%");
                    });
                }

                if ($typeFilter) {
                    $materialsQuery->where('type', $typeFilter);
                }

                $materials = $materialsQuery->get();

                if ($materials->isEmpty() && ($search || $typeFilter || $batchIdFilter)) {
                    return null;
                }

                return [
                    'id'              => $batch->id,
                    'title'           => $batch->title,
                    'code'            => formatBatchCode($batch->id),
                    'category'        => $batch->category->name ?? '-',
                    'status'          => $batch->status,
                    'materials'       => $materials->map(function ($material) {
                        return [
                            'id'          => $material->id,
                            'title'       => $material->title,
                            'type'        => $material->type,
                            'url'         => $material->url,
                            'description' => $material->description,
                            'uploaded_at' => formatDate($material->created_at),
                            'uploaded_by' => $material->uploaded_by_name ?? 'Unknown',
                            'type_badge'  => $material->type_badge,
                            'type_icon'   => $material->type_icon,
                        ];
                    }),
                    'materials_count' => $materials->count(),
                ];
            })
            ->filter()
            ->values();

        $statsQuery = BatchMaterial::whereHas('batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        });

        if ($batchIdFilter) {
            $statsQuery->where('batch_id', $batchIdFilter);
        }

        $statsData = $statsQuery->get();

        $stats = [
            'total'     => $statsData->count(),
            'pdf'       => $statsData->where('type', 'pdf')->count(),
            'video'     => $statsData->where('type', 'video')->count(),
            'recording' => $statsData->where('type', 'recording')->count(),
            'link'      => $statsData->where('type', 'link')->count(),
        ];

        return view('trainer.upload-materi', compact('batches', 'stats', 'batchOptions'));
    }

    // store
    public function store(Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        $validated = $request->validate([
            'batch_id'    => 'required|exists:batches,id',
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:pdf,video,recording,link',
            'url'         => 'required|url|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->trainer_id !== $trainer->id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk batch ini')->withInput();
        }

        try {
            BatchMaterial::create([
                'batch_id'         => $validated['batch_id'],
                'title'            => $validated['title'],
                'type'             => $validated['type'],
                'url'              => $validated['url'],
                'description'      => $validated['description'] ?? null,
                'uploaded_by'      => $trainer->id,
                'uploaded_by_name' => $trainer->name,
            ]);

            return redirect()->route('trainer.upload-materi')->with('success', 'Materi berhasil diupload');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengupload materi: ' . $e->getMessage())->withInput();
        }
    }

    // destroy
    public function destroy(int $materialId): RedirectResponse
    {
        $trainer = Auth::user();

        try {
            $material = BatchMaterial::findOrFail($materialId);

            if ($material->batch->trainer_id !== $trainer->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus materi ini');
            }

            $material->delete();

            return redirect()->route('trainer.upload-materi')->with('success', 'Materi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus materi: ' . $e->getMessage());
        }
    }
}