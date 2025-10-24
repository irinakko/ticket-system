<?php

namespace App\Http\Controllers;

use App\Aggregates\TicketAggregate;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Category;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketLogs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $filters = $request->input('filters', []);

        $query = Ticket::with(['status', 'priority', 'categories', 'labels', 'user', 'creator'])
            ->visibleTo($user);

        foreach ($filters as $filter => $values) {
            if (empty($values)) {
                continue;
            }

            switch ($filter) {
                case 'status':
                    $query->whereIn('status_id', $values);
                    break;
                case 'priority':
                    $query->whereIn('priority_id', $values);
                    break;
                case 'categories':
                    $query->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $values));
                    break;
                case 'labels':
                    $query->whereHas('labels', fn ($q) => $q->whereIn('labels.id', $values));
                    break;
                case 'assignee':
                    $query->whereHas('user', fn ($q) => $q->whereIn('users.id', $values));
                    break;
                case 'created_by':
                    $query->whereHas('creator', fn ($q) => $q->whereIn('users.id', $values));
                    break;
                case 'name':
                    $query->whereLike('title', $values);
                    break;
            }
        }

        $tickets = $query->paginate(8)->through(fn ($ticket) => [
            'id' => $ticket->id,
            'name' => $ticket->title,
            'status' => $ticket->status->name,
            'priority' => $ticket->priority->name,
            'assigned_to' => $ticket->user ? ['id' => $ticket->user->id, 'name' => $ticket->user->name] : null,
            'created_by' => $ticket->creator ? ['id' => $ticket->creator->id, 'name' => $ticket->creator->name] : null,
            'categories' => $ticket->categories->pluck('name'),
            'labels' => $ticket->labels->pluck('name'),
            'created_at' => $ticket->created_at->toDateTimeString(),
        ]);

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'filterOptions' => [
                'status' => Status::select('id', 'name')->get(),
                'priority' => Priority::select('id', 'name')->get(),
                'categories' => Category::select('id', 'name')->get(),
                'labels' => Label::select('id', 'name')->get(),
                'assignee' => User::select('id', 'name')->get(),
                'created_by' => User::select('id', 'name')->get(),
                'name' => Ticket::select('title')->distinct()->pluck('title'),
            ],
            'appliedFilters' => $filters,
        ]);
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

    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $data['assignee_id'];
        unset($data['assignee_id']);
        $ticket = Ticket::create($data);

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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $details = $this->getTicketDetails($ticket);
        TicketAggregate::retrieve($ticket->id)
            ->createTicket($ticket->id, $user->id, $details)
            ->persist();

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

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $user = Auth::user();

        $ticket = Ticket::visibleTo($user)->findOrFail($ticket->id);

        $ticket->fill([
            'title' => $request->input('name'),
            'description' => $request->input('description'),
            'priority_id' => $request->input('priority_id'),
            'status_id' => $request->input('status_id'),
            'user_id' => $request->input('assignee_id'),
            'created_by' => $user->id,
        ]);

        $ticketChanged = $ticket->isDirty();

        if ($ticketChanged) {
            $ticket->save();
        }

        $ticket->labels()->sync($request->input('label_ids', []));
        $ticket->categories()->sync($request->input('category_ids', []));

        if ($request->filled('attachment_ids')) {
            TicketAttachment::where('ticket_id', $ticket->id)->update(['ticket_id' => null]);

            TicketAttachment::whereIn('id', $request->input('attachment_ids'))->update([
                'ticket_id' => $ticket->id,
            ]);
        }

        $attachmentsAdded = false;
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');

                $ticket->attachments()->create([
                    'file_path' => $path,
                ]);
            }
            $attachmentsAdded = true;
        }

        if ($ticketChanged || $attachmentsAdded) {
            $details = $this->getTicketDetails($ticket);

            TicketAggregate::retrieve($ticket->id)
                ->updateTicket($ticket->id, $user->id, $details)
                ->persist();
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        /** @var User $user */
        $user = Auth::user();

        $ticket = Ticket::with(['status', 'categories', 'labels', 'priority', 'user'])
            ->visibleTo($user)
            ->findOrFail($ticket->id);
        $details = $this->getTicketDetails($ticket);
        TicketAggregate::retrieve($ticket->id)
            ->deleteTicket($ticket->id, $user->id, $details)
            ->persist();
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    private function getTicketDetails(Ticket $ticket): array
    {
        return [
            'title' => $ticket->title,
            'status' => optional($ticket->status)->name,
            'categories' => $ticket->categories->pluck('name')->toArray(),
            'labels' => $ticket->labels->pluck('name')->toArray(),
            'priority' => optional($ticket->priority)->name,
            'assignee' => optional($ticket->user)->name,
            'description' => $ticket->description,
        ];
    }

    public function history()
    {
        $events = TicketLogs::orderByDesc('created_at')->get();

        return view('tickets.ticket-logs.index', compact('events'));
    }

    public function logs()
    {
        $logs = TicketLogs::latest()->paginate(50);

        return view('tickets.ticket-logs.index', [
            'logs' => $logs,
            'filters' => [],
        ]);
    }
}
