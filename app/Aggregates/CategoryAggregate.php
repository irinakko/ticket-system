<?php

namespace App\Aggregates;

use App\Events\Domain\CategoryEvents\CategoryCreated;
use App\Events\Domain\CategoryEvents\CategoryDeleted;
use App\Events\Domain\CategoryEvents\CategoryUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class CategoryAggregate extends AggregateRoot
{
    private bool $deleted = false;

    private array $attributes = [];

    public function createCategory(int $categoryId, int $userId, array $attributes): self
    {
        $this->recordThat(new CategoryCreated(
            categoryId: $categoryId,
            userId: $userId,
            attributes: $attributes
        ));

        return $this;
    }

    public function updateCategory(int $categoryId, array $oldAttributes, array $newAttributes): self
    {
        if ($this->deleted) {
            throw new \Exception('Cannot update a deleted category');
        }

        $this->recordThat(new CategoryUpdated(
            categoryId: $categoryId,
            oldAttributes: $oldAttributes,
            newAttributes: $newAttributes
        ));

        return $this;
    }

    public function deleteCategory(int $categoryId, array $attributes): self
    {
        if ($this->deleted) {
            throw new \Exception('Category already deleted');
        }

        $this->recordThat(new CategoryDeleted(
            categoryId: $categoryId,
            attributes: $attributes
        ));

        return $this;
    }

    protected function applyCategoryCreated(CategoryCreated $event): void
    {
        $this->attributes = $event->attributes;
        $this->deleted = false;
    }

    protected function applyCategoryUpdated(CategoryUpdated $event): void
    {
        $this->attributes = $event->newAttributes;
    }

    protected function applyCategoryDeleted(CategoryDeleted $event): void
    {
        $this->deleted = true;
    }
}
