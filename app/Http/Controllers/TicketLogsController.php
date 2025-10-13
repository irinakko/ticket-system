<?php

namespace App\Http\Controllers;

use App\Models\TicketLogs;
use Inertia\Inertia;

class TicketLogsController extends Controller
{
    public function index()
    {
        return Inertia::render('Tickets/TicketLogs/Index', [
            'logsTable' => TicketLogs::make()]
        );
    }
}
