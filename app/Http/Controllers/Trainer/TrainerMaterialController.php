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
    /**
     * Display materials management page
     */
    public function index(Request $request): View
    {
        $trainer = Auth::user();

        // Verify user is a trainer
        if (!RoleHelper::isTrainer($trainer)) {
            abort(403, 'Unauthorized access');
        }

        // Get filter parameters
        $search = $request->input('search');
        $batchIdFilter = $request->input('batch_id');
        $typeFilter = $request->input('type');

        // Query materials with filters
        $materialsQuery = BatchMaterial::whereHas('batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        });

        // Apply search filter
        if ($search) {
            $materialsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply batch filter
        if ($batchIdFilter) {
            $materialsQuery->where('batch_id', $batchIdFilter);
        }

        // Apply type filter
        if ($typeFilter) {
            $materialsQuery->where('type', $typeFilter);
        }

        // Get filtered materials
        $allMaterials = $materialsQuery->get();

        // Get trainer's batches with filtered materials
        $batches = Batch::where('trainer_id', $trainer->id)
            ->with(['category'])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($batch) use ($search, $batchIdFilter, $typeFilter) {
                // Filter materials for this batch
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

                // Only include batch if it has materials after filtering OR no filter applied
                if ($materials->isEmpty() && ($search || $typeFilter || $batchIdFilter)) {
                    return null;
                }

                return [
                    'id' => $batch->id,
                    'title' => $batch->title,
                    'code' => formatBatchCode($batch->id),
                    'category' => $batch->category->name ?? '-',
                    'materials' => $materials->map(function ($material) {
                        return [
                            'id' => $material->id,
                            'title' => $material->title,
                            'type' => $material->type,
                            'url' => $material->url,
                            'description' => $material->description,
                            'uploaded_at' => formatDate($material->created_at),
                            'uploaded_by' => $material->uploaded_by_name ?? 'Unknown',
                            'type_badge' => $material->type_badge,
                            'type_icon' => $material->type_icon,
                        ];
                    }),
                    'materials_count' => $materials->count(),
                ];
            })
            ->filter() // Remove null batches
            ->values(); // Re-index array

        // Calculate statistics (from all materials without search filter, but with type filter)
        $statsQuery = BatchMaterial::whereHas('batch', function ($query) use ($trainer) {
            $query->where('trainer_id', $trainer->id);
        });

        if ($batchIdFilter) {
            $statsQuery->where('batch_id', $batchIdFilter);
        }

        $statsData = $statsQuery->get();

        $stats = [
            'total' => $statsData->count(),
            'pdf' => $statsData->where('type', 'pdf')->count(),
            'video' => $statsData->where('type', 'video')->count(),
            'recording' => $statsData->where('type', 'recording')->count(),
            'link' => $statsData->where('type', 'link')->count(),
        ];

        // Get batches for dropdown (only batches that trainer owns)
        $batchOptions = Batch::where('trainer_id', $trainer->id)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($batch) {
                return [
                    'value' => $batch->id,
                    'label' => $batch->title . ' - ' . formatBatchCode($batch->id),
                ];
            });

        return view('trainer.upload-materi', compact(
            'batches',
            'stats',
            'batchOptions'
        ));
    }

    /**
     * Store a new material
     */
    public function store(Request $request): RedirectResponse
    {
        $trainer = Auth::user();

        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:pdf,video,recording,link',
            'url' => 'required|url|max:500',
            'description' => 'nullable|string|max:1000',
        ], [
            'batch_id.required' => 'Batch harus dipilih',
            'batch_id.exists' => 'Batch tidak valid',
            'title.required' => 'Judul materi harus diisi',
            'title.max' => 'Judul materi maksimal 255 karakter',
            'type.required' => 'Tipe materi harus dipilih',
            'type.in' => 'Tipe materi tidak valid',
            'url.required' => 'URL/Link harus diisi',
            'url.url' => 'Format URL tidak valid',
            'url.max' => 'URL maksimal 500 karakter',
        ]);

        // Verify trainer owns the batch
        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->trainer_id !== $trainer->id) {
            return redirect()
                ->back()
                ->with('error', 'Anda tidak memiliki akses untuk batch ini')
                ->withInput();
        }

        try {
            BatchMaterial::create([
                'batch_id' => $validated['batch_id'],
                'title' => $validated['title'],
                'type' => $validated['type'],
                'url' => $validated['url'],
                'description' => $validated['description'] ?? null,
                'uploaded_by' => $trainer->id,
                'uploaded_by_name' => $trainer->name,
            ]);

            return redirect()
                ->route('trainer.upload-materi')
                ->with('success', 'Materi berhasil diupload');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengupload materi: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete a material
     */
    public function destroy(int $materialId): RedirectResponse
    {
        $trainer = Auth::user();

        try {
            $material = BatchMaterial::findOrFail($materialId);

            // Verify trainer owns the batch
            if ($material->batch->trainer_id !== $trainer->id) {
                return redirect()
                    ->back()
                    ->with('error', 'Anda tidak memiliki akses untuk menghapus materi ini');
            }

            $material->delete(); // Soft delete

            return redirect()
                ->route('trainer.upload-materi')
                ->with('success', 'Materi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus materi: ' . $e->getMessage());
        }
    }

    /**
     * Get material types for dropdown
     */
    public function getTypes(): array
    {
        return [
            ['value' => 'pdf', 'label' => 'PDF'],
            ['value' => 'video', 'label' => 'Video'],
            ['value' => 'recording', 'label' => 'Recording'],
            ['value' => 'link', 'label' => 'Link'],
        ];
    }
}