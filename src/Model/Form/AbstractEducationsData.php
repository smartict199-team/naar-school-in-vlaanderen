<?php

declare(strict_types=1);

namespace App\Model\Form;

use App\Entity\SchoolEducation;

abstract class AbstractEducationsData
{
    /**
     * @var array<int, array<array-key, SchoolEducation>>
     */
    private array $educationsCollections = [];

    final public function __construct()
    {
    }

    /**
     * @return array<int, array<array-key, SchoolEducation>>
     */
    public function getEducationsCollections(): array
    {
        return $this->educationsCollections;
    }

    /**
     * @param array<int, array<array-key, SchoolEducation>> $educationsCollections
     */
    public function setEducationsCollections(array $educationsCollections): void
    {
        $this->educationsCollections = $educationsCollections;
    }
}
