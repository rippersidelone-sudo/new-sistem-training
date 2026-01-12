<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        // Start query with counts
        $query = Category::withCount([
            'batches',
            'prerequisites',
            'dependents'
        ]);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Prerequisite filter
        if ($request->filled('prerequisite_filter')) {
            $filter = $request->input('prerequisite_filter');
            if ($filter === 'with') {
                $query->has('prerequisites');
            } elseif ($filter === 'without') {
                $query->doesntHave('prerequisites');
            }
        }

        // Get paginated results
        $categories = $query->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        // Statistics for cards
        $totalCategories = Category::count();
        $withoutPrerequisite = Category::doesntHave('prerequisites')->count();
        $withPrerequisite = Category::has('prerequisites')->count();

        // Get all categories for prerequisite selection (excluding current in edit)
        $allCategories = Category::orderBy('name')->get();

        return view('coordinator.kategori-pelatihan', compact(
            'categories',
            'totalCategories',
            'withoutPrerequisite',
            'withPrerequisite',
            'allCategories'
        ));
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['required', 'string'],
            'prerequisites' => ['nullable', 'array'],
            'prerequisites.*' => ['exists:categories,id'],
        ]);

        DB::beginTransaction();
        try {
            // Create category
            $category = Category::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            // Attach prerequisites if provided
            if (!empty($validated['prerequisites'])) {
                // Validate no circular dependency
                foreach ($validated['prerequisites'] as $prereqId) {
                    if ($this->wouldCreateCircularDependency($category->id, $prereqId)) {
                        DB::rollBack();
                        return back()->withErrors([
                            'prerequisites' => 'Prerequisite yang dipilih akan membuat circular dependency.'
                        ])->withInput();
                    }
                }
                
                $category->prerequisites()->attach($validated['prerequisites']);
            }

            DB::commit();

            return redirect()->route('coordinator.categories.index')
                ->with('success', 'Kategori "' . $category->name . '" berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan kategori.'])->withInput();
        }
    }

    /**
     * Display the specified category
     */
    public function show(Category $category)
    {
        $category->load([
            'prerequisites',
            'dependents',
            'batches' => function($query) {
                $query->withCount('batchParticipants')
                      ->orderBy('start_date', 'desc')
                      ->limit(10);
            }
        ]);

        // Jika ada file khusus detail kategori, ganti sesuai file. Jika tidak, bisa abaikan atau buat file baru jika dibutuhkan.
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => ['required', 'string'],
            'prerequisites' => ['nullable', 'array'],
            'prerequisites.*' => ['exists:categories,id', 'not_in:' . $category->id],
        ]);

        DB::beginTransaction();
        try {
            // Update category
            $category->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            // Sync prerequisites
            $newPrerequisites = $validated['prerequisites'] ?? [];
            
            // Validate no circular dependency for new prerequisites
            foreach ($newPrerequisites as $prereqId) {
                if ($this->wouldCreateCircularDependency($category->id, $prereqId)) {
                    DB::rollBack();
                    return back()->withErrors([
                        'prerequisites' => 'Prerequisite yang dipilih akan membuat circular dependency.'
                    ])->withInput();
                }
            }

            $category->prerequisites()->sync($newPrerequisites);

            DB::commit();

            return redirect()->route('coordinator.categories.index')
                ->with('success', 'Kategori "' . $category->name . '" berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui kategori.'])->withInput();
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy(Category $category)
    {
        // Check if category has batches
        if ($category->batches()->count() > 0) {
            return back()->withErrors([
                'error' => 'Kategori "' . $category->name . '" tidak dapat dihapus karena masih memiliki batch terkait.'
            ]);
        }

        // Check if category is prerequisite for other categories
        if ($category->dependents()->count() > 0) {
            return back()->withErrors([
                'error' => 'Kategori "' . $category->name . '" tidak dapat dihapus karena menjadi prerequisite untuk kategori lain.'
            ]);
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('coordinator.categories.index')
            ->with('success', 'Kategori "' . $categoryName . '" berhasil dihapus!');
    }

    /**
     * Check if adding prerequisite would create circular dependency
     * 
     * @param int $categoryId - The category we're adding prerequisite to
     * @param int $prerequisiteId - The prerequisite we want to add
     * @return bool
     */
    private function wouldCreateCircularDependency(int $categoryId, int $prerequisiteId): bool
    {
        // If prerequisite is the category itself
        if ($categoryId === $prerequisiteId) {
            return true;
        }

        // Get all prerequisites of the prerequisite (recursive check)
        $prereqCategory = Category::find($prerequisiteId);
        if (!$prereqCategory) {
            return false;
        }

        // Check if current category is already a prerequisite of the new prerequisite
        // This would create a circular dependency: A requires B, B requires A
        $allPrerequisitesOfPrereq = $this->getAllPrerequisites($prereqCategory);
        
        return in_array($categoryId, $allPrerequisitesOfPrereq);
    }

    /**
     * Get all prerequisites recursively
     * 
     * @param Category $category
     * @param array $visited - Track visited categories to prevent infinite loop
     * @return array
     */
    private function getAllPrerequisites(Category $category, array $visited = []): array
    {
        // Prevent infinite loop
        if (in_array($category->id, $visited)) {
            return [];
        }

        $visited[] = $category->id;
        $allPrereqs = [];

        foreach ($category->prerequisites as $prereq) {
            $allPrereqs[] = $prereq->id;
            $allPrereqs = array_merge(
                $allPrereqs, 
                $this->getAllPrerequisites($prereq, $visited)
            );
        }

        return array_unique($allPrereqs);
    }
}