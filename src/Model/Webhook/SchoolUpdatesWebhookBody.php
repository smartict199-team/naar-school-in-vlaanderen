<?php

declare(strict_types=1);

namespace App\Model\Webhook;

class SchoolUpdatesWebhookBody
{
    public ?int $schoolId;
    public ?int $startYear;
    public ?int $endYear;
    /**
     * @var int[]
     */
    public array $establishmentNumbers;
}
