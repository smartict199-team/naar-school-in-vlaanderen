<?php

declare(strict_types=1);

namespace App\Model\Form;

use App\Entity\SchoolEducation;
use App\Entity\SchoolGrade;
use App\Entity\SchoolLevel;
use App\Entity\SchoolPhase;
use App\Entity\SchoolType;
use App\Entity\SchoolYear;

class FilterForm
{
    /**
     * @var SchoolCityChoice[]
     */
    private array $cities = [];

    private ?SchoolYear $schoolYear = null;

    private ?string $level = null;

    private ?string $type = null;

    private ?SchoolEducation $education = null;

    private ?SchoolGrade $schoolGrade = null;

    private ?SchoolLevel $schoolLevel = null;

    private ?SchoolType $schoolType = null;

    private ?SchoolPhase $schoolPhase = null;

    /**
     * @return SchoolCityChoice[]
     */
    public function getCities(): array
    {
        return $this->cities;
    }

    /**
     * @param SchoolCityChoice[] $cities
     */
    public function setCities(array $cities): void
    {
        $this->cities = $cities;
    }

    public function getSchoolYear(): ?SchoolYear
    {
        return $this->schoolYear;
    }

    public function setSchoolYear(?SchoolYear $schoolYear): void
    {
        $this->schoolYear = $schoolYear;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): void
    {
        $this->level = $level;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getEducation(): ?SchoolEducation
    {
        return $this->education;
    }

    public function setEducation(?SchoolEducation $education): void
    {
        $this->education = $education;
    }

    public function getSchoolGrade(): ?SchoolGrade
    {
        return $this->schoolGrade;
    }

    public function setSchoolGrade(?SchoolGrade $schoolGrade): void
    {
        $this->schoolGrade = $schoolGrade;
    }

    public function getSchoolLevel(): ?SchoolLevel
    {
        return $this->schoolLevel;
    }

    public function setSchoolLevel(?SchoolLevel $schoolLevel): void
    {
        $this->schoolLevel = $schoolLevel;
    }

    public function getSchoolType(): ?SchoolType
    {
        return $this->schoolType;
    }

    public function setSchoolType(?SchoolType $schoolType): void
    {
        $this->schoolType = $schoolType;
    }

    public function getSchoolPhase(): ?SchoolPhase
    {
        return $this->schoolPhase;
    }

    public function setSchoolPhase(?SchoolPhase $schoolPhase): void
    {
        $this->schoolPhase = $schoolPhase;
    }
}
