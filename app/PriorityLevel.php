<?php

namespace App;

enum PriorityLevel: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function color(): string
    {
        return match ($this) {
            self::Low => '#60a5fa',
            self::Medium => '#facc15',
            self::High => '#f87171',
        };
    }
}
