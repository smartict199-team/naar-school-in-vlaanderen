<?php

declare(strict_types=1);

namespace App\Model\Api\LevelEducations\SchoolNumbers;

class UnderrepresentedGroupNumbers
{
    public ?int $id;
    public ?string $name;
    public ?int $capacity;
    public ?int $studentSeatsTaken;
    public ?float $studentSeatsPercentage;

    public function __construct()
    {
    }
}
