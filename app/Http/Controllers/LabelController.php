<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LabelController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $filters = $request->input('filters', []);

        $query = Label::visibleTo($user);

        if (! empty($filters['name'])) {
            $query->whereIn('name', $filters['name']);
        }

        $labels = $query->get();

        $names = Label::select('name')->distinct()->pluck('name');

        return Inertia::render('Labels/Index', [
            'labels' => $labels,
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

        Label::create($validated);

        return redirect()->route('labels.index');
    }

    public function update(Request $request, Label $label)
    {
        $label->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('labels.index');
    }

    public function destroy(Label $label)
    {
        $label->delete();

        return redirect()->route('labels.index');
    }
}
