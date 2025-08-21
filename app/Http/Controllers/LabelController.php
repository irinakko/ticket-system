<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index()
    {
        $labels = Label::all();

        return view('labels.index', compact('labels'));
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
