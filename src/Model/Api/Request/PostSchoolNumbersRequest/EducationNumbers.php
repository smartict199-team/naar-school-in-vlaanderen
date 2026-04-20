<?php

declare(strict_types=1);

namespace App\Model\Api\Request\PostSchoolNumbersRequest;

use Symfony\Component\Validator\Constraints as Assert;

class EducationNumbers
{
    /**
     * @Assert\NotNull()
     */
    public ?int $educationId = null;

    /**
     * @Assert\LessThan(value="2147483647")
     */
    public ?int $capacity = null;
    public ?float $indicatorStudentSeatsPercentage = null;

    /**
     * @Assert\LessThan(value="2147483647")
     */
    public ?int $indicatorStudentSeatsTaken = null;

    /**
     * @Assert\LessThan(value="2147483647")
     */
    public ?int $studentSeatsTaken = null;
    public ?bool $indicatorStudentSeatsPercentageVisible = null;
    public ?bool $capacityReached = null;
    public ?string $capacityReachedAt = null;

    /**
     * @Assert\Valid()
     *
     * @var UnderrepresentedGroupNumbers[]
     */
    public array $underrepresentedGroupNumbers = [];
}
