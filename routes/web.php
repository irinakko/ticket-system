<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware([\App\Http\Middleware\HasRole::class.':admin'])->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('labels', LabelController::class);
        Route::resource('tickets', TicketController::class);
        Route::resource('users', UserController::class);
    });

    Route::middleware([\App\Http\Middleware\HasRole::class.':admin,user,agent'])->group(function () {
        Route::resource('tickets', TicketController::class);

    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');
});

require __DIR__.'/auth.php';
