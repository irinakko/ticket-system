<?php

namespace App\Models;

use App\PriorityLevel;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    protected $table = 'priorities';

    protected $fillable = ['name', 'color'];

    public function getLevelEnum(): PriorityLevel
    {
        return PriorityLevel::from($this->name);
    }

    public static function getByEnum(PriorityLevel $level): ?self
    {
        return static::where('name', $level->value)->first();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'priority_id');
    }
}
