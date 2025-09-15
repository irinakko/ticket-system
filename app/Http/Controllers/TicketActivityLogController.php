<?php

namespace App\Http\Controllers;

use App\Models\TicketActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketActivityLogController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $filtersInput = $request->input('filters', []);

        $query = TicketActivityLog::with(['user', 'ticket'])
            ->visibleTo($user)
            ->latest();

        if (! empty($filtersInput['user'])) {
            $query->whereIn('user_id', $filtersInput['user']);
        }

        if (! empty($filtersInput['ticket'])) {
            $query->whereIn('ticket_id', $filtersInput['ticket']);
        }

        if (! empty($filtersInput['date'])) {
            $query->whereDate('created_at', $filtersInput['date']);
        }

        if (! empty($filtersInput['action'])) {
            $query->whereIn('action', $filtersInput['action']);
        }

        if (! empty($filtersInput['details'])) {
            $query->where('details', 'like', '%'.$filtersInput['details'].'%');
        }

        $logs = $query->get()->map(function ($log) {
            $log->name = $log->ticket->title ?? 'N/A';

            return $log;
        });

        $users = User::all();

        $tickets = $logs->pluck('ticket')->filter()->unique('id')->map(function ($ticket) {
            $ticket->name = $ticket->title;

            return $ticket;
        })->values();

        $actions = $logs->pluck('action')->unique()->map(function ($action) {
            return (object) [
                'id' => $action,
                'name' => ucfirst($action),
            ];
        })->values();

        $dates = $logs->map(function ($log) {
            return $log->created_at->format('Y-m-d');
        })->unique()->map(function ($date) {
            return (object) [
                'id' => $date,
                'name' => $date,
            ];
        })->values();

        $filters = [
            'user' => $users,
            'ticket' => $tickets,
            'action' => $actions,
            'date' => $dates,
        ];

        return view('tickets.ticket-logs.index', compact('logs', 'filters'));
    }

    public function show()
    {
        //
    }
}
