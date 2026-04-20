<?php

declare(strict_types=1);

namespace App\Model\Response;

class PasswordChangeTicketResponse implements ResponseInterface
{
    private ?string $ticket = null;

    public function getTicket(): ?string
    {
        return $this->ticket;
    }

    public function setTicket(?string $ticket): void
    {
        $this->ticket = $ticket;
    }
}
