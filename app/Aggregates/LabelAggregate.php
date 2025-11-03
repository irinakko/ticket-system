<?php

namespace App\Aggregates;

use App\Events\Domain\LabelEvents\LabelCreated;
use App\Events\Domain\LabelEvents\LabelDeleted;
use App\Events\Domain\LabelEvents\LabelUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LabelAggregate extends AggregateRoot
{
    private bool $deleted = false;

    private array $attributes = [];

    public function createLabel(int $labelId, int $userId, array $attributes): self
    {
        $this->recordThat(new LabelCreated(
            labelId: $labelId,
            userId: $userId,
            attributes: $attributes
        ));

        return $this;
    }

    public function updateLabel(int $labelId, array $oldAttributes, array $newAttributes): self
    {
        if ($this->deleted) {
            throw new \Exception('Cannot update a deleted label');
        }

        $this->recordThat(new LabelUpdated(
            labelId: $labelId,
            oldAttributes: $oldAttributes,
            newAttributes: $newAttributes
        ));

        return $this;
    }

    public function deleteLabel(int $labelId, array $attributes): self
    {
        if ($this->deleted) {
            throw new \Exception('Label already deleted');
        }

        $this->recordThat(new LabelDeleted(
            labelId: $labelId,
            attributes: $attributes
        ));

        return $this;
    }

    // Apply methods
    protected function applyLabelCreated(LabelCreated $event): void
    {
        $this->attributes = $event->attributes;
        $this->deleted = false;
    }

    protected function applyLabelUpdated(LabelUpdated $event): void
    {
        $this->attributes = $event->newAttributes;
    }

    protected function applyLabelDeleted(LabelDeleted $event): void
    {
        $this->deleted = true;
    }
}
