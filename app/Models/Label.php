<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $fillable = ['name'];

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_label', 'label_id', 'ticket_id');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }
    }
}
