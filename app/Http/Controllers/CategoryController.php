<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $filters = $request->input('filters', []);

        $query = Category::visibleTo($user);

        // Filter by name
        if (! empty($filters['name'])) {
            $query->whereIn('name', $filters['name']);
        }

        $categories = $query->get();

        // For filter dropdown
        $names = Category::select('name')->distinct()->pluck('name');

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
            'name' => $names,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        Category::create([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('categories.index');
    }

    public function show(Category $category)
    {
        //
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $category->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index');
    }
}
