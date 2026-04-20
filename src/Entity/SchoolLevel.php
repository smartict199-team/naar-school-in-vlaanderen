<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_school_levels")
 */
class SchoolLevel
{
    public const TYPE_PRE_PRIMARY_EDUCATION = 'pre_primary_education';
    public const TYPE_PRIMARY_EDUCATION = 'primary_education';
    public const TYPE_SECONDARY_EDUCATION = 'secondary_education';

    public const LEVEL_REGULAR_EDUCATION = 'regular_education';
    public const LEVEL_SPECIAL_NEEDS_EDUCATION = 'special_needs_education';
    public const LEVEL_RECEPTION_EDUCATION = 'reception_education';

    public const INTERNAL_NAME_PRE_PRIMARY_REGULAR_EDUCATION = 'pre_primary_regular_education';
    public const INTERNAL_NAME_SECONDARY_REGULAR_EDUCATION_FIRST_GRADE = 'secondary_regular_education_first_grade';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $internalName = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $type = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $level = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $capacityRequired = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $capacityReachedRequired = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $capacityReachedAtRequired = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $indicatorStudentSeatsPercentageRequired = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $indicatorStudentSeatsRequired = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $studentSeatsRequired = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $finalityRequired = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $formTypeRequired = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $administrativeGroupsRequired = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $typeRequired = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $nameRequired = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $gradeRequired = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $phaseRequired = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $maxEducations = null;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $defaultRequired = false;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $defaultDeletable = true;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $position = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SchoolLevelType", mappedBy="schoolLevel")
     *
     * @var Collection<array-key, SchoolLevelType>
     */
    private Collection $types;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SchoolLevelFormType", mappedBy="schoolLevel")
     *
     * @var Collection<array-key, SchoolLevelFormType>
     */
    private Collection $formTypes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SchoolLevelGrade", mappedBy="schoolLevel")
     *
     * @var Collection<array-key, SchoolLevelGrade>
     */
    private Collection $grades;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SchoolLevelPhase", mappedBy="schoolLevel")
     *
     * @var Collection<array-key, SchoolLevelPhase>
     */
    private Collection $phases;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $phaseFilterVisible = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $gradeFilterVisible = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $educationsFilterVisible = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $schoolTypeFilterVisible = false;

    public function __construct()
    {
        $this->types = new ArrayCollection();
        $this->formTypes = new ArrayCollection();
        $this->grades = new ArrayCollection();
        $this->phases = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getInternalName(): ?string
    {
        return $this->internalName;
    }

    public function setInternalName(?string $internalName): void
    {
        $this->internalName = $internalName;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function isCapacityRequired(): bool
    {
        return $this->capacityRequired;
    }

    public function setCapacityRequired(bool $capacityRequired): void
    {
        $this->capacityRequired = $capacityRequired;
    }

    public function isIndicatorStudentSeatsPercentageRequired(): bool
    {
        return $this->indicatorStudentSeatsPercentageRequired;
    }

    public function setIndicatorStudentSeatsPercentageRequired(bool $indicatorStudentSeatsPercentageRequired): void
    {
        $this->indicatorStudentSeatsPercentageRequired = $indicatorStudentSeatsPercentageRequired;
    }

    public function isIndicatorStudentSeatsRequired(): bool
    {
        return $this->indicatorStudentSeatsRequired;
    }

    public function setIndicatorStudentSeatsRequired(bool $indicatorStudentSeatsRequired): void
    {
        $this->indicatorStudentSeatsRequired = $indicatorStudentSeatsRequired;
    }

    public function isStudentSeatsRequired(): bool
    {
        return $this->studentSeatsRequired;
    }

    public function setStudentSeatsRequired(bool $studentSeatsRequired): void
    {
        $this->studentSeatsRequired = $studentSeatsRequired;
    }

    public function isFinalityRequired(): bool
    {
        return $this->finalityRequired;
    }

    public function setFinalityRequired(bool $finalityRequired): void
    {
        $this->finalityRequired = $finalityRequired;
    }

    public function isFormTypeRequired(): bool
    {
        return $this->formTypeRequired;
    }

    public function setFormTypeRequired(bool $formTypeRequired): void
    {
        $this->formTypeRequired = $formTypeRequired;
    }

    public function isAdministrativeGroupsRequired(): bool
    {
        return $this->administrativeGroupsRequired;
    }

    public function setAdministrativeGroupsRequired(bool $administrativeGroupsRequired): void
    {
        $this->administrativeGroupsRequired = $administrativeGroupsRequired;
    }

    public function isTypeRequired(): bool
    {
        return $this->typeRequired;
    }

    public function setTypeRequired(bool $typeRequired): void
    {
        $this->typeRequired = $typeRequired;
    }

    public function isNameRequired(): bool
    {
        return $this->nameRequired;
    }

    public function setNameRequired(bool $nameRequired): void
    {
        $this->nameRequired = $nameRequired;
    }

    public function isGradeRequired(): bool
    {
        return $this->gradeRequired;
    }

    public function setGradeRequired(bool $gradeRequired): void
    {
        $this->gradeRequired = $gradeRequired;
    }

    public function isPhaseRequired(): bool
    {
        return $this->phaseRequired;
    }

    public function setPhaseRequired(bool $phaseRequired): void
    {
        $this->phaseRequired = $phaseRequired;
    }

    public function getMaxEducations(): ?int
    {
        return $this->maxEducations;
    }

    public function setMaxEducations(?int $maxEducations): void
    {
        $this->maxEducations = $maxEducations;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return Collection<array-key, SchoolLevelType>
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    /**
     * @param Collection<array-key, SchoolLevelType> $types
     */
    public function setTypes(Collection $types): void
    {
        $this->types = $types;
    }

    public function isDefaultRequired(): bool
    {
        return $this->defaultRequired;
    }

    public function setDefaultRequired(bool $defaultRequired): void
    {
        $this->defaultRequired = $defaultRequired;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): void
    {
        $this->level = $level;
    }

    /**
     * @return Collection<array-key, SchoolLevelFormType> $formTypes
     */
    public function getFormTypes(): Collection
    {
        return $this->formTypes;
    }

    /**
     * @param Collection<array-key, SchoolLevelFormType> $formTypes
     */
    public function setFormTypes(Collection $formTypes): void
    {
        $this->formTypes = $formTypes;
    }

    /**
     * @return Collection<array-key, SchoolLevelGrade>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    /**
     * @param Collection<array-key, SchoolLevelGrade> $grades
     */
    public function setGrades(Collection $grades): void
    {
        $this->grades = $grades;
    }

    public function getDefaultGrade(): ?SchoolGrade
    {
        foreach ($this->grades as $levelGrade) {
            if ($levelGrade->isDefault()) {
                return $levelGrade->getSchoolGrade();
            }
        }

        return null;
    }

    public function getDefaultType(): ?SchoolType
    {
        foreach ($this->types as $levelType) {
            if ($levelType->isDefault()) {
                return $levelType->getSchoolType();
            }
        }

        return null;
    }

    public function isPrePrimaryEducation(): bool
    {
        return self::TYPE_PRE_PRIMARY_EDUCATION === $this->type;
    }

    public function isPhaseFilterVisible(): bool
    {
        return $this->phaseFilterVisible;
    }

    public function setPhaseFilterVisible(bool $phaseFilterVisible): void
    {
        $this->phaseFilterVisible = $phaseFilterVisible;
    }

    public function isGradeFilterVisible(): bool
    {
        return $this->gradeFilterVisible;
    }

    public function setGradeFilterVisible(bool $gradeFilterVisible): void
    {
        $this->gradeFilterVisible = $gradeFilterVisible;
    }

    public function isEducationsFilterVisible(): bool
    {
        return $this->educationsFilterVisible;
    }

    public function setEducationsFilterVisible(bool $educationsFilterVisible): void
    {
        $this->educationsFilterVisible = $educationsFilterVisible;
    }

    public function isSchoolTypeFilterVisible(): bool
    {
        return $this->schoolTypeFilterVisible;
    }

    public function setSchoolTypeFilterVisible(bool $schoolTypeFilterVisible): void
    {
        $this->schoolTypeFilterVisible = $schoolTypeFilterVisible;
    }

    /**
     * @return iterable<string>
     */
    public function getRequiredEducationFields(): iterable
    {
        if (true === $this->administrativeGroupsRequired) {
            yield 'administrative_groups';
        }

        if (true === $this->nameRequired) {
            yield 'name';
        }

        if (true === $this->typeRequired) {
            yield 'type';
        }

        if (true === $this->finalityRequired) {
            yield 'finality';
        }

        if (true === $this->formTypeRequired) {
            yield 'form_type';
        }

        if (true === $this->gradeRequired) {
            yield 'grade';
        }

        if (true === $this->phaseRequired) {
            yield 'phase';
        }
    }

    /**
     * @return iterable<string>
     */
    public function getRequiredNumbersFields(): iterable
    {
        if (true === $this->capacityRequired) {
            yield 'capacity';
        }

        if (true === $this->indicatorStudentSeatsPercentageRequired) {
            yield 'indicator_student_seats_percentage';
        }

        if (true === $this->indicatorStudentSeatsRequired) {
            yield 'indicator_student_seats_taken';
        }

        if (true === $this->studentSeatsRequired) {
            yield 'student_seats_taken';
        }

        yield 'capacity_reached';
        yield 'capacity_reached_at';
    }

    /**
     * @return Collection<array-key, SchoolGrade>
     */
    public function getHiddenGrades(): Collection
    {
        $criteria = new Criteria();
        $criteria->where(new Comparison('visibleFrontend', '=', false));
        $grades = $this->grades;
        if ($grades instanceof Selectable) {
            return $grades->matching($criteria);
        }
        throw new \RuntimeException('Could not get hidden grades for level');
    }

    /**
     * @return Collection<array-key, SchoolLevelPhase>
     */
    public function getPhases(): Collection
    {
        return $this->phases;
    }

    /**
     * @param Collection<array-key, SchoolLevelPhase> $phases
     */
    public function setPhases(Collection $phases): void
    {
        $this->phases = $phases;
    }

    public function isCapacityReachedRequired(): bool
    {
        return $this->capacityReachedRequired;
    }

    public function setCapacityReachedRequired(bool $capacityReachedRequired): void
    {
        $this->capacityReachedRequired = $capacityReachedRequired;
    }

    public function isCapacityReachedAtRequired(): bool
    {
        return $this->capacityReachedAtRequired;
    }

    public function setCapacityReachedAtRequired(bool $capacityReachedAtRequired): void
    {
        $this->capacityReachedAtRequired = $capacityReachedAtRequired;
    }

    public function isReceptionEducation(): bool
    {
        return self::LEVEL_RECEPTION_EDUCATION === $this->level;
    }

    public function isDefaultDeletable(): bool
    {
        return $this->defaultDeletable;
    }

    public function setDefaultDeletable(bool $defaultDeletable): void
    {
        $this->defaultDeletable = $defaultDeletable;
    }
}
