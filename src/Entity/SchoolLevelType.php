<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_school_levels_types")
 */
class SchoolLevelType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolLevel", inversedBy="types")
     * @ORM\JoinColumn(name="school_level_id")
     */
    private ?SchoolLevel $schoolLevel = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolType", inversedBy="levelTypes")
     * @ORM\JoinColumn(name="school_type_id")
     */
    private ?SchoolType $schoolType = null;

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

    public function getSchoolType(): ?SchoolType
    {
        return $this->schoolType;
    }

    public function setSchoolType(?SchoolType $schoolType): void
    {
        $this->schoolType = $schoolType;
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
