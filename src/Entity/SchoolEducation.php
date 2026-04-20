<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use function Symfony\Component\String\u;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_school_educations")
 * @ORM\HasLifecycleCallbacks()
 */
class SchoolEducation
{
    public const CAPACITY_FIELDS = [
        'indicator_student_seats_percentage',
        'indicator_student_seats_taken',
        'student_seats_taken',
        'capacity_reached',
        'capacity_reached_at',
        'capacity',
    ];

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
    private ?string $administrativeGroups = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\School", inversedBy="educations")
     * @ORM\JoinColumn(name="school_id", nullable=true)
     */
    private ?School $school = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolFormType")
     * @ORM\JoinColumn(name="form_type_id", nullable=true)
     */
    private ?SchoolFormType $formType = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolType")
     * @ORM\JoinColumn(name="type_id", nullable=true)
     */
    private ?SchoolType $type = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolLevel")
     * @ORM\JoinColumn(name="level_id", nullable=true)
     */
    private ?SchoolLevel $level = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolGrade")
     * @ORM\JoinColumn(name="grade_id", nullable=true)
     */
    private ?SchoolGrade $grade = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolPhase")
     * @ORM\JoinColumn(name="phase_id", nullable=true)
     */
    private ?SchoolPhase $phase = null;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $position = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolYear")
     * @ORM\JoinColumn(name="year_id")
     */
    private ?SchoolYear $year = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $capacity = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $indicatorStudentSeatsPercentage = null;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $indicatorStudentSeatsPercentageVisible = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $indicatorStudentSeatsTaken = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $studentSeatsTaken = null;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $capacityReached = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $capacityReachedAt = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $formTypeVisibleOnFrontend = true;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $finalityVisibleOnFrontend = true;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $capacityUpdatedAt = null;

    /**
     * @ORM\Column(name="is_default", type="boolean", nullable=false)
     */
    private bool $default = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolFinality")
     */
    private ?SchoolFinality $finality = null;

    /**
     * @var Collection<array-key, SchoolEducationUnderrepresentedGroup>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\SchoolEducationUnderrepresentedGroup", mappedBy="schoolEducation", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private Collection $underrepresentedGroups;

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

    public function getFullName(): ?string
    {
        $parts = [];
        if ($this->level instanceof SchoolLevel && $this->level->isNameRequired()) {
            $parts[] = $this->name;
        }

        $parts[] = $this->getSubTitle();

        return implode(' - ', array_filter($parts));
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAdministrativeGroups(): ?string
    {
        return $this->administrativeGroups;
    }

    public function setAdministrativeGroups(?string $administrativeGroups): void
    {
        $this->administrativeGroups = $administrativeGroups;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): void
    {
        $this->school = $school;
    }

    public function getFormType(): ?SchoolFormType
    {
        return $this->formType;
    }

    public function setFormType(?SchoolFormType $formType): void
    {
        $this->formType = $formType;
    }

    public function getType(): ?SchoolType
    {
        return $this->type;
    }

    public function setType(?SchoolType $type): void
    {
        $this->type = $type;
    }

    public function getLevel(): ?SchoolLevel
    {
        return $this->level;
    }

    public function setLevel(?SchoolLevel $level): void
    {
        $this->level = $level;
    }

    public function getGrade(): ?SchoolGrade
    {
        return $this->grade;
    }

    public function setGrade(?SchoolGrade $grade): void
    {
        $this->grade = $grade;
    }

    public function getPhase(): ?SchoolPhase
    {
        return $this->phase;
    }

    public function setPhase(?SchoolPhase $phase): void
    {
        $this->phase = $phase;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getYear(): ?SchoolYear
    {
        return $this->year;
    }

    public function setYear(?SchoolYear $year): void
    {
        $this->year = $year;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): void
    {
        $this->capacity = $capacity;
    }

    public function getIndicatorStudentSeatsPercentage(): ?float
    {
        return $this->indicatorStudentSeatsPercentage;
    }

    public function setIndicatorStudentSeatsPercentage(?float $indicatorStudentSeatsPercentage): void
    {
        $this->indicatorStudentSeatsPercentage = $indicatorStudentSeatsPercentage;
    }

    public function isIndicatorStudentSeatsPercentageVisible(): bool
    {
        return $this->indicatorStudentSeatsPercentageVisible;
    }

    public function setIndicatorStudentSeatsPercentageVisible(bool $indicatorStudentSeatsPercentageVisible): void
    {
        $this->indicatorStudentSeatsPercentageVisible = $indicatorStudentSeatsPercentageVisible;
    }

    public function getIndicatorStudentSeatsTaken(): ?int
    {
        return $this->indicatorStudentSeatsTaken;
    }

    public function setIndicatorStudentSeatsTaken(?int $indicatorStudentSeatsTaken): void
    {
        $this->indicatorStudentSeatsTaken = $indicatorStudentSeatsTaken;
    }

    public function getStudentSeatsTaken(): ?int
    {
        return $this->studentSeatsTaken;
    }

    public function setStudentSeatsTaken(?int $studentSeatsTaken): void
    {
        $this->studentSeatsTaken = $studentSeatsTaken;
    }

    public function isCapacityReached(): bool
    {
        return $this->capacityReached;
    }

    public function setCapacityReached(bool $capacityReached): void
    {
        $this->capacityReached = $capacityReached;
    }

    public function getCapacityReachedAt(): ?\DateTime
    {
        return $this->capacityReachedAt;
    }

    public function setCapacityReachedAt(?\DateTime $capacityReachedAt): void
    {
        $this->capacityReachedAt = $capacityReachedAt;
    }

    public function isFormTypeVisibleOnFrontend(): bool
    {
        return $this->formTypeVisibleOnFrontend;
    }

    public function setFormTypeVisibleOnFrontend(bool $formTypeVisibleOnFrontend): void
    {
        $this->formTypeVisibleOnFrontend = $formTypeVisibleOnFrontend;
    }

    public function isFinalityVisibleOnFrontend(): bool
    {
        return $this->finalityVisibleOnFrontend;
    }

    public function setFinalityVisibleOnFrontend(bool $finalityVisibleOnFrontend): void
    {
        $this->finalityVisibleOnFrontend = $finalityVisibleOnFrontend;
    }

    public function getCapacityUpdatedAt(): ?\DateTime
    {
        return $this->capacityUpdatedAt;
    }

    public function setCapacityUpdatedAt(?\DateTime $capacityUpdatedAt): void
    {
        $this->capacityUpdatedAt = $capacityUpdatedAt;
    }

    public function getSubTitle(bool $frontend = false): string
    {
        $labelPieces = [];
        $level = $this->level;
        if (null === $level) {
            return (string) $this->id;
        }

        if (true === $frontend) {
            $labelPieces[] = $level->getName();
        }

        $type = $this->type;
        if (null !== $type && true === $level->isTypeRequired()) {
            $labelPieces[] = $type->getName();
        }

        $finality = $this->finality;
        if (null !== $finality && true === $level->isFinalityRequired()) {
            if (false === $frontend || true === $this->finalityVisibleOnFrontend) {
                $labelPieces[] = $finality->getName();
            }
        }

        $formType = $this->formType;
        if (null !== $formType && true === $level->isFormTypeRequired()) {
            if (false === $frontend || true === $this->formTypeVisibleOnFrontend) {
                $labelPieces[] = $formType->getName();
            }
        }

        $grade = $this->grade;
        if (null !== $grade && true === $level->isGradeRequired()) {
            $labelPieces[] = $grade->getName();
        }

        $phase = $this->phase;
        if (null !== $phase && true === $level->isPhaseRequired()) {
            $labelPieces[] = $phase->getName();
        }

        $labelPieces = array_unique($labelPieces);

        return implode(' - ', array_filter($labelPieces));
    }

    /**
     * @return iterable<string>
     */
    public function getRequiredFields(): iterable
    {
        $level = $this->level;
        if (null === $level) {
            return;
        }

        if (true === $level->isNameRequired()) {
            yield 'name';
        }

        if (true === $level->isTypeRequired()) {
            yield 'type';
        }

        if (true === $level->isFormTypeRequired()) {
            yield 'formType';
        }

        if (true === $level->isGradeRequired()) {
            yield 'grade';
        }

        if (true === $level->isPhaseRequired()) {
            yield 'phase';
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateUpdatedAt(PreUpdateEventArgs $args): void
    {
        foreach ($args->getEntityChangeSet() as $field => $value) {
            $fieldName = u($field)->snake()->toString();
            if (!\in_array($fieldName, self::CAPACITY_FIELDS)) {
                continue;
            }

            $this->setCapacityUpdatedAt(new \DateTime());

            return;
        }
    }

    public function getRemainingIndicatorSeats(): int
    {
        return (int) round(((int) $this->indicatorStudentSeatsPercentage / 100) * (int) $this->getCapacity()) - (int) $this->getIndicatorStudentSeatsTaken();
    }

    public function getRemainingSeats(): int
    {
        return (int) $this->getCapacity() - (int) $this->getStudentSeatsTaken() - (int) $this->getIndicatorStudentSeatsTaken();
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }

    public function isDeletable(): bool
    {
        $level = $this->level;
        if ($level instanceof SchoolLevel && true === $this->default) {
            return $level->isDefaultDeletable();
        }

        return true;
    }

    public function getIndicatorSeats(): int
    {
        return (int) round($this->capacity * $this->indicatorStudentSeatsPercentage / 100);
    }

    public function getFinality(): ?SchoolFinality
    {
        return $this->finality;
    }

    public function setFinality(?SchoolFinality $finality): void
    {
        $this->finality = $finality;
    }

    /**
     * @return Collection<array-key, SchoolEducationUnderrepresentedGroup>
     */
    public function getUnderrepresentedGroups(): Collection
    {
        if (!isset($this->underrepresentedGroups)) {
            $this->underrepresentedGroups = new ArrayCollection();
        }
        return $this->underrepresentedGroups;
    }

    public function setUnderrepresentedGroups(Collection $underrepresentedGroups): void
    {
        $this->underrepresentedGroups = $underrepresentedGroups;
    }

    public function getUnderrepresentedGroup(int $underrepresentedGroupId): ?SchoolEducationUnderrepresentedGroup
    {
        if (!isset($this->underrepresentedGroups)) {
            $this->underrepresentedGroups = new ArrayCollection();
        }

        $criteria = new Criteria();
        $criteria->andWhere(new Comparison('id', '=', $underrepresentedGroupId));

        $underrepresentedGroups = $this->underrepresentedGroups;
        if ($underrepresentedGroups instanceof Selectable) {
            return $underrepresentedGroups->matching($criteria)->first();
        }

        throw new \RuntimeException('Could not get educations for level');
    }

    public function addUnderrepresentedGroup(SchoolEducationUnderrepresentedGroup $underrepresentedGroup): void
    {
        $this->underrepresentedGroups->add($underrepresentedGroup);
        $underrepresentedGroup->setSchoolEducation($this);
    }

    public function removeUnderrepresentedGroup(?int $id): void
    {
        $criteria = new Criteria();
        $criteria->andWhere(new Comparison('id', '=', $id));

        $underrepresentedGroups = $this->underrepresentedGroups;
        if (!$underrepresentedGroups instanceof Selectable) {
            return;
        }

        $underrepresentedGroup = $underrepresentedGroups->matching($criteria)->first();

        if (!$underrepresentedGroup instanceof SchoolEducationUnderrepresentedGroup) {
            return;
        }

        $this->underrepresentedGroups->removeElement($underrepresentedGroup);
        $underrepresentedGroup->setSchoolEducation(null);
    }
}
