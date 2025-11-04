<?php

namespace App\Http\Controllers;

use App\Aggregates\CategoryAggregate;
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

        if (! empty($filters['name'])) {
            $query->whereIn('name', $filters['name']);
        }

        $labels = $query->get()->map(fn ($label) => [
            'id' => $label->id,
            'name' => $label->name,
        ]);

        $names = Category::select('name')->distinct()->pluck('name');

        return Inertia::render('Categories/Index', [
            'categories' => [
                'data' => $labels,
            ],
            'filters' => [
                'name' => $names,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $categoryId = Category::max('id') + 1;

        CategoryAggregate::retrieve($categoryId)
            ->createCategory($categoryId, $request->user()->id, $validated)
            ->persist();

        return redirect()->route('categories.index');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $oldAttributes = ['name' => $category->name];
        $newAttributes = $validated;

        CategoryAggregate::retrieve($category->id)
            ->updateCategory($category->id, $oldAttributes, $newAttributes)
            ->persist();

        return redirect()->route('labels.index');
    }

    public function destroy(Category $category)
    {
        $attributes = ['name' => $category->name];

        CategoryAggregate::retrieve($category->id)
            ->deleteCategory($category->id, $attributes)
            ->persist();

        return redirect()->route('categories.index');
    }
}
