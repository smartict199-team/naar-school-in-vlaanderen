<?php

declare(strict_types=1);

namespace App\Model;

interface LoggableUserInterface
{
    public function getId();

    public function getLoggableUserName(): string;
}
