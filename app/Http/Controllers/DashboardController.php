<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('Dashboard', [
            'user' => $user,
            'ticketsByStatus' => $this->getTicketsByStatus(),
            'ticketsByCreatedAt' => $this->getTicketsByCreatedAt($user),
            'usersByRole' => $this->getUsersByRole(),
        ]);
    }

    private function getTicketsByStatus()
    {
        $statuses = Status::all();
        $labels = $statuses->pluck('name')->toArray();
        $data = $statuses->map(fn ($status) => $status->tickets()->count())->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => ['#f87171', '#34d399', '#60a5fa', '#fbbf24', '#a78bfa', '#f472b6'],
                ],
            ],
        ];
    }

    private function getTicketsByCreatedAt(User $user)
    {
        $items = $user->tickets()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get()
            ->reverse()
            ->values();

        $labels = $items->pluck('date')->toArray();
        $data = $items->pluck('count')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => ['#60a5fa', '#f87171', '#34d399', '#fbbf24', '#a78bfa', '#f472b6', '#818cf8'],
                ],
            ],
        ];
    }

    private function getUsersByRole()
    {
        $roles = Role::all();
        $roleCounts = User::selectRaw('role_id, COUNT(*) as count')
            ->groupBy('role_id')
            ->get()
            ->keyBy('role_id');

        $labels = $roles->pluck('name')->toArray();
        $data = $roles->map(fn ($role) => $roleCounts[$role->id]->count ?? 0)->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => ['#f87171', '#34d399', '#60a5fa', '#fbbf24', '#a78bfa', '#f472b6'],
                ],
            ],
        ];
    }
}
