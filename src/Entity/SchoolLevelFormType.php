<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_school_levels_form_types")
 */
class SchoolLevelFormType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolLevel", inversedBy="formTypes")
     * @ORM\JoinColumn(name="school_level_id")
     */
    private ?SchoolLevel $schoolLevel = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolFormType", inversedBy="levelFormTypes")
     * @ORM\JoinColumn(name="school_type_id")
     */
    private ?SchoolFormType $schoolFormType = null;

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

    public function getSchoolFormType(): ?SchoolFormType
    {
        return $this->schoolFormType;
    }

    public function setSchoolFormType(?SchoolFormType $schoolFormType): void
    {
        $this->schoolFormType = $schoolFormType;
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
