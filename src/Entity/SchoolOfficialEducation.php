<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SchoolOfficialEducationRepository")
 * @ORM\Table(name="app_school_official_educations")
 */
class SchoolOfficialEducation
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $establishmentNumber = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolFormType")
     * @ORM\JoinColumn(name="form_type_id", nullable=true)
     */
    private ?SchoolFormType $formType = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\SchoolLevel")
     * @ORM\JoinColumn(name="level_id", nullable=true)
     */
    private ?SchoolLevel $level = null;

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

    public function getFormType(): ?SchoolFormType
    {
        return $this->formType;
    }

    public function setFormType(?SchoolFormType $formType): void
    {
        $this->formType = $formType;
    }

    public function getLevel(): ?SchoolLevel
    {
        return $this->level;
    }

    public function setLevel(?SchoolLevel $level): void
    {
        $this->level = $level;
    }

    public function getEstablishmentNumber(): ?int
    {
        return $this->establishmentNumber;
    }

    public function setEstablishmentNumber(?int $establishmentNumber): void
    {
        $this->establishmentNumber = $establishmentNumber;
    }
}
