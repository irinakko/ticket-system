<?php

namespace App\Events\Domain\TicketEvents;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TicketDeleted extends ShouldBeStored
{
    public function __construct(
        public int $ticketId,
        public int $userId,
        public array $details,
    ) {}
}
