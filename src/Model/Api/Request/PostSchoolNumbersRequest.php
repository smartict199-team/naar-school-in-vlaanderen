<?php

declare(strict_types=1);

namespace App\Model\Api\Request;

use App\Model\Api\Request\PostSchoolNumbersRequest\EducationNumbers;
use Symfony\Component\Validator\Constraints as Assert;

class PostSchoolNumbersRequest
{
    /**
     * @Assert\NotNull()
     */
    public ?int $establishmentNumber = null;

    /**
     * @Assert\NotNull()
     */
    public ?int $startYear = null;

    /**
     * @Assert\NotNull()
     */
    public ?int $endYear = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Valid()
     *
     * @var EducationNumbers[]
     */
    public array $educationNumbers = [];
}
