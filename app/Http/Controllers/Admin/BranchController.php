<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount('users')->orderBy('name')->get();
        return view('admin.branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:branches'],
            'address' => ['nullable', 'string'],
        ]);

        Branch::create($request->all());

        return redirect()->route('admin.branches.index')
            ->with('success', 'Cabang berhasil ditambahkan!');
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:branches,name,' . $branch->id],
            'address' => ['nullable', 'string'],
        ]);

        $branch->update($request->all());

        return redirect()->route('admin.branches.index')
            ->with('success', 'Cabang berhasil diperbarui!');
    }

    public function destroy(Branch $branch)
    {
        // Prevent deleting if branch has users
        if ($branch->users()->count() > 0) {
            return back()->withErrors(['error' => 'Cabang tidak dapat dihapus karena masih memiliki user!']);
        }

        $branch->delete();

        return redirect()->route('admin.branches.index')
            ->with('success', 'Cabang berhasil dihapus!');
    }
}