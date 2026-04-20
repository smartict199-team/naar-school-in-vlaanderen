<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_school_levels_phases")
 */
class SchoolLevelPhase
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolLevel", inversedBy="phases")
     * @ORM\JoinColumn(name="school_level_id")
     */
    private ?SchoolLevel $schoolLevel = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolPhase", inversedBy="levelPhases")
     * @ORM\JoinColumn(name="school_phase_id")
     */
    private ?SchoolPhase $schoolPhase = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $position = null;

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

    public function getSchoolPhase(): ?SchoolPhase
    {
        return $this->schoolPhase;
    }

    public function setSchoolPhase(?SchoolPhase $schoolPhase): void
    {
        $this->schoolPhase = $schoolPhase;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }
}
