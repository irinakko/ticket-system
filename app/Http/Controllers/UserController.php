<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {   /** @var User $user */
        $user = Auth::user();
        $filters = $request->input('filters', []);

        $query = User::visibleTo($user);

        if (! empty($filters['name'])) {
            $query->whereIn('name', $filters['name']);
        }

        if (! empty($filters['email'])) {
            $query->whereIn('email', $filters['email']);
        }

        $users = $query->get();

        $names = User::select('name')->distinct()->pluck('name');
        $emails = User::select('email')->distinct()->pluck('email');

        return Inertia::render('Users/Index', [
            'users' => $users,
            'name' => $names,
            'email' => $emails,
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);

        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        //
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->only('name', 'email'));

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
