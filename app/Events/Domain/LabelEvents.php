<?php

namespace App\Events\Domain\LabelEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LabelCreated extends ShouldBeStored
{
    public function __construct(
        public int $labelId,
        public int $userId,
        public array $attributes
    ) {}
}

class LabelUpdated extends ShouldBeStored
{
    public function __construct(
        public int $labelId,
        public array $oldAttributes,
        public array $newAttributes
    ) {}
}

class LabelDeleted extends ShouldBeStored
{
    public function __construct(
        public int $labelId,
        public array $attributes
    ) {}
}
