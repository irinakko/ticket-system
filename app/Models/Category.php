<?php

namespace App\Models;

use App\Role as RoleEnum;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'category_ticket', 'category_id', 'ticket_id');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->hasRole(RoleEnum::Admin->value)) {
            return $query;
        }
    }
}
