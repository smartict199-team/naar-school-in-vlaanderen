<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_school_education_underrepresented_groups")
 */
class SchoolEducationUnderrepresentedGroup
{
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
    private ?string $description = null;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $position = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $capacity = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $studentSeatsPercentage = null;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $studentSeatsPercentageVisible = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $studentSeatsTaken = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolEducation", inversedBy="underrepresentedGroups")
     * @ORM\JoinColumn(name="school_education_id", nullable=true)
     */
    private ?SchoolEducation $schoolEducation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): void
    {
        $this->capacity = $capacity;
    }

    public function getStudentSeatsPercentage(): ?float
    {
        return $this->studentSeatsPercentage;
    }

    public function setStudentSeatsPercentage(?float $studentSeatsPercentage): void
    {
        $this->studentSeatsPercentage = $studentSeatsPercentage;
    }

    public function isStudentSeatsPercentageVisible(): bool
    {
        return $this->studentSeatsPercentageVisible;
    }

    public function setStudentSeatsPercentageVisible(bool $studentSeatsPercentageVisible): void
    {
        $this->studentSeatsPercentageVisible = $studentSeatsPercentageVisible;
    }

    public function getStudentSeatsTaken(): ?int
    {
        return $this->studentSeatsTaken;
    }

    public function setStudentSeatsTaken(?int $studentSeatsTaken): void
    {
        $this->studentSeatsTaken = $studentSeatsTaken;
    }

    public function getSchoolEducation(): ?SchoolEducation
    {
        return $this->schoolEducation;
    }

    public function setSchoolEducation(?SchoolEducation $schoolEducation): void
    {
        $this->schoolEducation = $schoolEducation;
    }
}
