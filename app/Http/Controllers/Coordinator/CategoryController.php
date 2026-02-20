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

        // Sort filter
        $sort = $request->input('sort', 'latest');
        if ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'name_desc') {
            $query->orderBy('name', 'desc');
        }

        // Get paginated results
        $categories = $query->paginate(12)
            ->withQueryString();

        // Statistics for cards
        $totalCategories = Category::count();
        $withoutPrerequisite = Category::doesntHave('prerequisites')->count();
        $withPrerequisite = Category::has('prerequisites')->count();

        // Get all categories for prerequisite selection (excluding current in edit)
        $allCategories = Category::orderBy('name')->get();

        return view('coordinator.kategori-pelatihan.kategori-pelatihan', compact(
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
        
        DB::beginTransaction();
        try {
            // Remove all prerequisite relationships
            $category->prerequisites()->detach();
            
            // Delete the category
            $category->delete();
            
            DB::commit();

            return redirect()->route('coordinator.categories.index')
                ->with('success', 'Kategori "' . $categoryName . '" berhasil dihapus!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat menghapus kategori.'
            ]);
        }
    }

    /**
     * Check if adding prerequisite would create circular dependency
     */
    private function wouldCreateCircularDependency(int $categoryId, int $prerequisiteId): bool
    {
        if ($categoryId === $prerequisiteId) {
            return true;
        }

        $prereqCategory = Category::find($prerequisiteId);
        if (!$prereqCategory) {
            return false;
        }

        $allPrerequisitesOfPrereq = $this->getAllPrerequisites($prereqCategory);
        
        return in_array($categoryId, $allPrerequisitesOfPrereq);
    }

    /**
     * Get all prerequisites recursively
     */
    private function getAllPrerequisites(Category $category, array $visited = []): array
    {
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