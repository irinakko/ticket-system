<?php

namespace App\Models;

use App\PriorityLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority_id',
        'status_id',
        'user_id',
    ];

    public function getPriorityLevelEnum(): ?PriorityLevel
    {
        return $this->priority ? $this->priority->getLevelEnum() : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class, 'priority_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_ticket', 'ticket_id', 'category_id');
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'label_ticket', 'ticket_id', 'label_id');
    }
}
