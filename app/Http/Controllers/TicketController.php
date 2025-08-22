<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['status', 'labels', 'categories'])->get();

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create', [
            'categories' => Category::all(),
            'priorities' => Priority::all(),
            'statuses' => Status::all(),
            'users' => User::all()]);
    }

    public function store(Request $request)
    {
        $ticket = Ticket::create([
            'title' => $request->input('name'),
            'description' => $request->input('description'),
            'priority_id' => $request->input('priority_id'),
            'status_id' => $request->input('status_id'),
            'category_id' => $request->input('category_id'),
            'user_id' => $request->input('assignee_id'),
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        //
    }

    public function edit(Ticket $ticket)
    {
        $statuses = Status::all();
        $categories = Category::all();
        $priorities = Priority::all();
        $users = User::all();

        return view('tickets.edit', compact('ticket', 'statuses', 'categories', 'priorities', 'users'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $ticket->update([
            'name' => $request->input('name'),
            'status_id' => $request->input('status_id'),
            'category_id' => $request->input('category_id'),
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }
}
