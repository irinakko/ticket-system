<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $filters = $request->input('filters', []);

        $query = Ticket::with(['status', 'labels', 'categories', 'user', 'priority', 'attachments'])
            ->visibleTo($user);

        if (! empty($filters['name'])) {
            $query->where('title', 'like', '%'.$filters['name'].'%');
        }

        if (! empty($filters['priority'])) {
            $query->whereIn('priority_id', $filters['priority']);
        }

        if (! empty($filters['status'])) {
            $query->whereIn('status_id', $filters['status']);
        }

        if (! empty($filters['categories'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->whereIn('categories.id', $filters['categories']);
            });
        }

        if (! empty($filters['labels'])) {
            $query->whereHas('labels', function ($q) use ($filters) {
                $q->whereIn('labels.id', $filters['labels']);
            });
        }

        if (! empty($filters['assignee'])) {
            $query->whereIn('user_id', $filters['assignee']);
        }

        $tickets = $query->get()->map(function ($ticket) {
            $ticket->name = $ticket->title;

            return $ticket;
        });

        $priorities = Priority::all();
        $statuses = Status::all();
        $categories = Category::all();
        $labels = Label::all();
        $assignees = User::all();

        return view('tickets.index', compact(
            'tickets',
            'priorities',
            'statuses',
            'categories',
            'labels',
            'assignees'
        ));
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
            'created_by' => 'nullable|exists:users,id',
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
            'created_by' => Auth::id(),
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
        /** @var User $user */
        $user = Auth::user();
        $title = str_replace('-', ' ', $title);
        $ticket = Ticket::with(['comments.user', 'status', 'priority', 'user', 'labels', 'categories', 'attachments'])
            ->where('title', $title)
            ->visibleTo($user)
            ->firstOrFail();

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        /** @var User $user */
        $user = Auth::user();
        $ticket = Ticket::visibleTo($user)->findOrFail($ticket->id);
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

        $ticket = Ticket::visibleTo($user)->findOrFail($ticket->id);

        $ticket->update([
            'title' => $request->input('name'),
            'description' => $request->input('description'),
            'priority_id' => $request->input('priority_id'),
            'status_id' => $request->input('status_id'),
            'user_id' => $request->input('assignee_id'),
            'created_by' => Auth::id(),
        ]);

        $ticket->labels()->sync($request->input('label_ids', []));
        $ticket->categories()->sync($request->input('category_ids', []));

        if ($request->filled('attachment_ids')) {
            TicketAttachment::where('ticket_id', $ticket->id)->update(['ticket_id' => null]);

            TicketAttachment::whereIn('id', $request->input('attachment_ids'))->update([
                'ticket_id' => $ticket->id,
            ]);
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');

                $ticket->attachments()->create([
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully.');
    }
}
