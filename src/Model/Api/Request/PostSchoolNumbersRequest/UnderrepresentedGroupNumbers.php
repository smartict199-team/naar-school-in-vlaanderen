<?php

declare(strict_types=1);

namespace App\Model\Api\Request\PostSchoolNumbersRequest;

class UnderrepresentedGroupNumbers
{
    public ?int $id = null;
    public ?string $name = null;
    public ?int $studentSeatsTaken = null;
    public ?float $studentSeatsPercentage = null;
    public bool $deleted = false;
}
