<?php

declare(strict_types=1);

namespace App\Event;

use App\Model\Request\UpdateUserRequest;
use Symfony\Contracts\EventDispatcher\Event;

class UserUpdatedEvent extends Event
{
    private UpdateUserRequest $request;
    private string $userId;

    public function __construct(string $userId, UpdateUserRequest $request)
    {
        $this->request = $request;
        $this->userId = $userId;
    }

    public function getRequest(): UpdateUserRequest
    {
        return $this->request;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
