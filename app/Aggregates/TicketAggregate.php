<?php

namespace App\Aggregates;

use App\Events\Domain\TicketEvents;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class TicketAggregate extends AggregateRoot
{
    public function createTicket(int $ticketId, int $userId, array $details): self
    {
        $this->recordThat(new TicketEvents\TicketCreated($ticketId, $userId, $details));

        return $this;
    }

    public function updateTicket(int $ticketId, int $userId, array $details): self
    {
        $this->recordThat(new TicketEvents\TicketUpdated($ticketId, $userId, $details));

        return $this;
    }

    public function deleteTicket(int $ticketId, int $userId, array $details): self
    {
        $this->recordThat(new TicketEvents\TicketDeleted($ticketId, $userId, $details));

        return $this;
    }
}
