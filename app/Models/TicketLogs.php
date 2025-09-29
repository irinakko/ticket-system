<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TicketLogs extends Model
{
    protected $table = 'stored_events';

    protected $fillable = [
        'aggregate_uuid',
        'aggregate_version',
        'event_class',
        'event_properties',
        'created_at',
        'event_version',
        'meta_data',
    ];

    protected $casts = [
        'event_properties' => 'array',
        'created_at' => 'datetime',
    ];

    public function getUserNameAttribute(): ?string
    {
        return optional(\App\Models\User::find($this->event_properties['userId'] ?? null))->name;
    }

    public function getChangesAttribute(): array
    {
        return $this->event_properties['changes'] ?? [];
    }

    public function getDetailsAttribute(): array
    {
        return $this->event_properties['details'] ?? [];
    }

    public function getActionAttribute(): string
    {
        return class_basename($this->event_class);
    }

    public function getTimestampAttribute(): Carbon
    {
        return $this->created_at;
    }
}
