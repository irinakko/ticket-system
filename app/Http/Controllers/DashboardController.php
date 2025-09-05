<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Role as RoleEnum;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {/** @var User $user */
        $user = Auth::user();
        if ($user->hasRole(RoleEnum::Admin->value)) {
            $ticketCounts = Ticket::selectRaw('statuses.name as status_name, COUNT(*) as count')
                ->join('statuses', 'tickets.status_id', '=', 'statuses.id')
                ->groupBy('statuses.name')
                ->pluck('count', 'status_name')
                ->toArray();

            return view('dashboard', compact('ticketCounts'));
        } else {
            return redirect()->route('tickets.index');
        }
    }
}
