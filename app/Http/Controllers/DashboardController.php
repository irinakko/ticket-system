<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {/** @var User $user */
        $user = Auth::user();
        $ticketQuery = Ticket::visibleTo($user);
        $ticketCounts = $ticketQuery->selectRaw('statuses.name as status_name, COUNT(*) as count')
            ->join('statuses', 'tickets.status_id', '=', 'statuses.id')
            ->groupBy('statuses.name')
            ->pluck('count', 'status_name')
            ->toArray();
        $userQuery = User::visibleTo($user);
        $userCounts = $userQuery->selectRaw('roles.name as role_name, COUNT(*) as count')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->groupBy('roles.name')
            ->pluck('count', 'role_name')
            ->toArray();

        return view('dashboard', compact('ticketCounts', 'userCounts'));
    }
}
