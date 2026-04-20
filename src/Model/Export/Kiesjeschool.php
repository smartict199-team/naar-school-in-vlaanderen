<?php

declare(strict_types=1);

namespace App\Model\Export;

use App\Entity\School;

class Kiesjeschool
{
    private School $school;

    /**
     * @var array<string, array<int, ?KiesjeschoolCapacity>>
     *
     * This array is indexed with
     *
     * The level:
     * SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION
     * SchoolLevel::TYPE_PRIMARY_EDUCATION
     *
     * The grade:
     * 0 = Instapklas
     * 1-3 = 1e - 3e kleuterklas
     * 1-6 = 1e - 6e leerjaar
     */
    private array $capacity = [];

    public function __construct(School $school)
    {
        $this->school = $school;
    }

    public function getSchool(): School
    {
        return $this->school;
    }

    public function setSchool(School $school): void
    {
        $this->school = $school;
    }

    public function getCapacity(string $level, int $grade): ?KiesjeschoolCapacity
    {
        if (!isset($this->capacity[$level]) || !isset($this->capacity[$level][$grade])) {
            return null;
        }

        return $this->capacity[$level][$grade];
    }

    /**
     * @return array<string, array<int, ?KiesjeschoolCapacity>>
     */
    public function getAllCapacities(): array
    {
        return $this->capacity;
    }

    public function setCapacity(string $level, int $grade, ?KiesjeschoolCapacity $capacity): void
    {
        if (!isset($this->capacity[$level])) {
            $this->capacity[$level] = [];
        }

        $this->capacity[$level][$grade] = $capacity;
    }
}
