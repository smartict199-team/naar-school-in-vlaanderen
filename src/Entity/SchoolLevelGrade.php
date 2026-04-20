<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_school_levels_grades")
 */
class SchoolLevelGrade
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolLevel", inversedBy="grades")
     * @ORM\JoinColumn(name="school_level_id")
     */
    private ?SchoolLevel $schoolLevel = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolGrade", inversedBy="levelGrades")
     * @ORM\JoinColumn(name="school_grade_id")
     */
    private ?SchoolGrade $schoolGrade = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $position = null;

    /**
     * @ORM\Column(type="boolean", nullable=false, name="is_default")
     */
    private bool $default = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getSchoolLevel(): ?SchoolLevel
    {
        return $this->schoolLevel;
    }

    public function setSchoolLevel(?SchoolLevel $schoolLevel): void
    {
        $this->schoolLevel = $schoolLevel;
    }

    public function getSchoolGrade(): ?SchoolGrade
    {
        return $this->schoolGrade;
    }

    public function setSchoolGrade(?SchoolGrade $schoolGrade): void
    {
        $this->schoolGrade = $schoolGrade;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    public function setDefault(bool $default): void
    {
        $this->default = $default;
    }
}
