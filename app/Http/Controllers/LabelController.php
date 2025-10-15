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

        // Filter by name
        if (! empty($filters['name'])) {
            $query->whereIn('name', $filters['name']);
        }

        $labels = $query->get();

        // For filter dropdown
        $names = Label::select('name')->distinct()->pluck('name');

        return Inertia::render('Labels/Index', [
            'labels' => $labels,
            'name' => $names,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return view('labels.create');
    }

    public function store(Request $request)
    {
        Label::create([
            'name' => $request->input('name'),
        ]);

        return redirect()->route('labels.index');
    }

    public function show(Label $label)
    {
        //
    }

    public function edit(Label $label)
    {
        return view('labels.edit', compact('label'));
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
