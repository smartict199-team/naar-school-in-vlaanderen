<?php

declare(strict_types=1);

namespace App\Model\Api;

class SchoolNumberResponse
{
    public ?int $id = null;
    public ?string $schoolName = null;
    public ?int $startYear = null;
    public ?int $endYear = null;

    /** @var array<int> */
    public array $establishmentNumbers = [];

    /** @var array<LevelEducations> */
    public array $levelEducations = [];
}
