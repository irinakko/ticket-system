<?php

namespace App\Http\Controllers;

use App\Aggregates\LabelAggregate;
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

        $labels = $query->get()->map(fn ($label) => [
            'id' => $label->id,
            'name' => $label->name,
        ]);

        $names = Label::select('name')->distinct()->pluck('name');

        return Inertia::render('Labels/Index', [
            'labels' => [
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

        $labelId = Label::max('id') + 1;

        LabelAggregate::retrieve($labelId)
            ->createLabel($labelId, $request->user()->id, $validated)
            ->persist();

        return redirect()->route('labels.index');
    }

    public function update(Request $request, Label $label)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $oldAttributes = ['name' => $label->name];
        $newAttributes = $validated;

        LabelAggregate::retrieve($label->id)
            ->updateLabel($label->id, $oldAttributes, $newAttributes)
            ->persist();

        return redirect()->route('labels.index');
    }

    public function destroy(Label $label)
    {
        $attributes = ['name' => $label->name];

        LabelAggregate::retrieve($label->id)
            ->deleteLabel($label->id, $attributes)
            ->persist();

        return redirect()->route('labels.index');
    }
}
