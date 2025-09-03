<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use App\Role as RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::all();

        return view('tickets.show', compact('comments'));
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
            'parent_id' => $request->input('parent_id'),
        ]);
        $ticket = Ticket::findOrFail($request->input('ticket_id'));

        return redirect()->route('tickets.show', ['ticket' => Str::slug($ticket->title)])
            ->with('success', 'Comment created successfully.');
    }

    public function show(Comment $comment)
    {
        //
    }

    public function edit(Comment $comment) {}

    public function update(Request $request, Comment $comment)
    {
        $comment->content = $request->input('content');
        $comment->save();

        return redirect()->route('tickets.show', Str::slug($comment->ticket->title))
            ->with('success', 'Comment updated successfully.');
    }

    public function destroy(Comment $comment)
    {
        $user = Auth::user();
        $user = $user instanceof User ? $user : User::find($user->id);
        if ($user->hasRole(RoleEnum::Admin->value) || $user->id === $comment->user_id) {
            $comment->delete();

            return redirect()->route('tickets.show', Str::slug($comment->ticket->title));
        }

        return redirect()->route('tickets.show', Str::slug($comment->ticket->title))
            ->with('error', 'You do not have permission to delete this comment.');
    }

    public function respond(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        Comment::create([
            'content' => $request->input('content'),
            'ticket_id' => $comment->ticket_id,
            'parent_id' => $comment->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('tickets.show', Str::slug($comment->ticket->title))
            ->with('success', 'Reply added successfully.');
    }
}
