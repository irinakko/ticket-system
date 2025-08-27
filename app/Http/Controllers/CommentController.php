<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use App\Role as RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::all();

        return view('comments.index', compact('comments'));
    }

    public function create(Request $request)
    {
        return view('comments.create', [
            'users' => User::all(),
            'tickets' => Ticket::all(),
        ]);
    }

    public function store(Request $request)
    {
        Comment::create([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
            'ticket_id' => $request->input('ticket_id'),
        ]);
        $ticket = Ticket::findOrFail($request->input('ticket_id'));

        return redirect()->route('tickets.show', ['title' => $ticket->title])
            ->with('success', 'Comment created successfully.');
    }

    public function show(Comment $comment)
    {
        //
    }

    public function edit(Comment $comment)
    {
        //
    }

    public function update(Request $request, Comment $comment)
    {
        //
    }

    public function destroy(Comment $comment)
    {
        /** @var User $user */
        $user = Auth::user();
        if (! ($user->hasRole(RoleEnum::Admin->value) || $user->id === $comment->user_id)) {
            $comment->delete();

            return redirect()->route('tickets.show', $comment->ticket_id);
        } else {
            return redirect()->route('tickets.show', $comment->ticket_id)->with('error', 'You do not have permission to delete this comment.');
        }
    }
}
