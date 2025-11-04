<?php

namespace App\Events\Domain\CategoryEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CategoryCreated extends ShouldBeStored
{
    public function __construct(
        public int $categoryId,
        public int $userId,
        public array $attributes
    ) {}
}

class CategoryUpdated extends ShouldBeStored
{
    public function __construct(
        public int $categoryId,
        public array $oldAttributes,
        public array $newAttributes
    ) {}
}

class CategoryDeleted extends ShouldBeStored
{
    public function __construct(
        public int $categoryId,
        public array $attributes
    ) {}
}
