<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketActivityLog extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'action', 'details', 'created_at'];

    protected $casts = [
        'details' => 'array',
    ];

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class)->withTrashed();
    }
}
