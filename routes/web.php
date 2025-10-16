<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketLogsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\HasRole;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth', HandleInertiaRequests::class)->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware([HasRole::class.':admin'], HandleInertiaRequests::class)->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
        Route::resource('labels', LabelController::class)->except(['show', 'create', 'edit']);
        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

        Route::get('/ticket-logs', [TicketLogsController::class, 'index'])->name('ticket-logs.index');
    });

    Route::middleware([HasRole::class.':admin,user,agent'], HandleInertiaRequests::class)->group(function () {
        Route::resource('tickets', TicketController::class);
        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
        Route::resource('comments', CommentController::class);
        Route::post('/comments/{comment}/respond', [CommentController::class, 'respond'])->name('comments.respond');

    });
});

require __DIR__.'/auth.php';
