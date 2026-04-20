<?php

declare(strict_types=1);

namespace App\Model\Api\LevelEducations;

use App\Model\Api\Details;
use App\Model\Api\LevelEducations\SchoolNumbers\UnderrepresentedGroupNumbers;

class EducationNumbers
{
    public ?int $id;
    public ?string $label = null;
    public ?string $administrativeGroup = null;
    public Details $grade;
    public Details $phase;
    public ?int $capacity = null;
    public ?float $indicatorStudentSeatsPercentage = null;
    public ?int $indicatorStudentSeatsTaken = null;
    public ?int $studentSeatsTaken = null;
    public ?bool $indicatorStudentSeatsPercentageVisible = null;
    public ?bool $capacityReached = null;
    public ?string $capacityReachedAt = null;
    public ?string $capacityUpdatedAt = null;

    /**
     * @var UnderrepresentedGroupNumbers[]
     */
    public array $underrepresentedGroupNumbers = [];

    public function __construct()
    {
        $this->grade = new Details();
        $this->phase = new Details();
    }
}
