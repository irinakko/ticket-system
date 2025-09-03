<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use App\Role as RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $ticketsQuery = Ticket::with(['status', 'labels', 'categories', 'user', 'priority', 'attachments']);

        $tickets = $user->hasRole(RoleEnum::Admin->value)
            ? $ticketsQuery->get()
            : $ticketsQuery->where('user_id', $user->id)->get();

        return view('tickets.index', compact('tickets'));
    }

    public function create(Request $request)
    {
        return view('tickets.create', [
            'categories' => Category::all(),
            'priorities' => Priority::all(),
            'statuses' => Status::all(),
            'labels' => Label::all(),
            'users' => User::all(),
            'attachments' => TicketAttachment::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority_id' => 'required|exists:priorities,id',
            'status_id' => 'required|exists:statuses,id',
            'assignee_id' => 'required|exists:users,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'label_ids' => 'nullable|array',
            'label_ids.*' => 'exists:labels,id',
            'attachments.*' => 'nullable|file|max:10240',
        ]);
        $ticket = Ticket::create(['title' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'priority_id' => $validated['priority_id'],
            'status_id' => $validated['status_id'],
            'user_id' => $validated['assignee_id'],
        ]);

        $ticket->categories()->sync($request->input('category_ids', []));
        $ticket->labels()->sync($request->input('label_ids', []));
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');

                $ticket->attachments()->create([
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');
    }

    public function show($title)
    {
        $title = str_replace('-', ' ', $title);
        $ticket = Ticket::with(['comments.user', 'status', 'priority', 'user', 'labels', 'categories', 'attachments'])
            ->where('title', $title)
            ->firstOrFail();

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->hasRole(RoleEnum::Admin->value)) {
            abort(403, 'Unauthorized');
        }
        $statuses = Status::all();
        $labels = Label::all();
        $categories = Category::all();
        $priorities = Priority::all();
        $users = User::all();
        $attachments = TicketAttachment::all();

        return view('tickets.edit', compact('ticket', 'statuses', 'categories', 'labels', 'priorities', 'users', 'attachments'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user->hasRole(RoleEnum::Admin->value)) {
            abort(403, 'Unauthorized');
        }
        $ticket->update([
            'title' => $request->input('name'),
            'description' => $request->input('description'),
            'priority_id' => $request->input('priority_id'),
            'label_id' => $request->input('label_id'),
            'status_id' => $request->input('status_id'),
            'category_id' => $request->input('category_id'),
            'user_id' => $request->input('assignee_id'),
            'attachment_id' => $request->input('attachment_id'),
        ]);
        $ticket->labels()->sync($request->input('label_ids', []));
        $ticket->categories()->sync($request->input('category_ids', []));
        $ticket->attachments()->sync($request->input('attachment_ids', []));

        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {  /** @var User $user */
        $user = Auth::user();
        if ($user->hasRole(RoleEnum::Admin->value)) {
            $ticket->delete();

            return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
        } else {
            abort(403, 'Unauthorized');
        }

    }
}
