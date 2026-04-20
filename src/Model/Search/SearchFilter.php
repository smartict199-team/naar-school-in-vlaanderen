<?php

declare(strict_types=1);

namespace App\Model\Search;

use App\Entity\SchoolEducation;
use App\Entity\SchoolGrade;
use App\Entity\SchoolLevel;
use App\Entity\SchoolPhase;
use App\Entity\SchoolType;
use App\Entity\SchoolYear;
use App\Model\Form\SchoolCityChoice;

class SearchFilter
{
    /**
     * @var array<SchoolCityChoice|null>
     */
    private array $cities = [];

    private ?SchoolYear $schoolYear = null;

    private ?string $level = null;

    private ?string $type = null;

    /**
     * @var SchoolEducation[]
     */
    private array $educations = [];

    /**
     * @var SchoolGrade[]
     */
    private array $schoolGrades = [];

    /**
     * @var SchoolLevel[]
     */
    private array $schoolLevels = [];

    /**
     * @var SchoolPhase[]
     */
    private array $schoolPhases = [];

    /**
     * @var SchoolType[]
     */
    private array $schoolTypes = [];

    /**
     * @return array<SchoolCityChoice|null>
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

    /**
     * @return string[]
     */
    public function getCityNames(): array
    {
        $names = [];

        foreach ($this->cities as $city) {
            if (!$city instanceof SchoolCityChoice) {
                continue;
            }

            $names[] = $city->getCity();
        }

        return $names;
    }

    /**
     * @return string[]
     */
    public function getCityPostalCodes(): array
    {
        $postalCodes = [];

        foreach ($this->cities as $city) {
            if (!$city instanceof SchoolCityChoice) {
                continue;
            }

            $postalCodes[] = $city->getPostalCode();
        }

        return $postalCodes;
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

    /**
     * @return SchoolEducation[]
     */
    public function getEducations(): array
    {
        return $this->educations;
    }

    /**
     * @param SchoolEducation[] $educations
     */
    public function setEducations(array $educations): void
    {
        $this->educations = $educations;
    }

    public function hasEducations(): bool
    {
        return !empty($this->educations);
    }

    /**
     * @return SchoolGrade[]
     */
    public function getSchoolGrades(): array
    {
        return $this->schoolGrades;
    }

    /**
     * @param SchoolGrade[] $schoolGrades
     */
    public function setSchoolGrades(array $schoolGrades): void
    {
        $this->schoolGrades = $schoolGrades;
    }

    public function hasSchoolGrades(): bool
    {
        return !empty($this->schoolGrades);
    }

    /**
     * @return SchoolLevel[]
     */
    public function getSchoolLevels(): array
    {
        return $this->schoolLevels;
    }

    /**
     * @param SchoolLevel[] $schoolLevels
     */
    public function setSchoolLevels(array $schoolLevels): void
    {
        $this->schoolLevels = $schoolLevels;
    }

    public function hasSchoolLevels(): bool
    {
        return !empty($this->schoolLevels);
    }

    /**
     * @return SchoolType[]
     */
    public function getSchoolTypes(): array
    {
        return $this->schoolTypes;
    }

    /**
     * @param SchoolType[] $schoolTypes
     */
    public function setSchoolTypes(array $schoolTypes): void
    {
        $this->schoolTypes = $schoolTypes;
    }

    public function hasSchoolTypes(): bool
    {
        return !empty($this->schoolTypes);
    }

    /**
     * @return SchoolPhase[]
     */
    public function getSchoolPhases(): array
    {
        return $this->schoolPhases;
    }

    /**
     * @param SchoolPhase[] $schoolPhases
     */
    public function setSchoolPhases(array $schoolPhases): void
    {
        $this->schoolPhases = $schoolPhases;
    }

    public function hasSchoolPhases(): bool
    {
        return !empty($this->schoolPhases);
    }

    public function hasCities(): bool
    {
        return !empty($this->cities);
    }

    /**
     * @return list<string|null>
     */
    public function getEducationNames(): array
    {
        $names = [];

        foreach ($this->educations as $education) {
            $names[] = $education->getName();
        }

        return $names;
    }

    /**
     * @return list<string|null>
     */
    public function getEducationAdministrativeGroups(): array
    {
        $groups = [];

        foreach ($this->educations as $education) {
            $groups[] = $education->getAdministrativeGroups();
        }

        return $groups;
    }

    public function addEducation(SchoolEducation $education): void
    {
        $this->educations[] = $education;
    }

    public function addSchoolGrade(SchoolGrade $schoolGrade): void
    {
        $this->schoolGrades[] = $schoolGrade;
    }

    public function addSchoolLevel(SchoolLevel $schoolLevel): void
    {
        $this->schoolLevels[] = $schoolLevel;
    }

    public function addSchoolPhase(SchoolPhase $schoolPhase): void
    {
        $this->schoolPhases[] = $schoolPhase;
    }

    public function addSchoolType(SchoolType $schoolType): void
    {
        $this->schoolTypes[] = $schoolType;
    }
}
