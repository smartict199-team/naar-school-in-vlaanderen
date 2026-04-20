<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_school_phases")
 */
class SchoolPhase
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
     * @ORM\OneToMany(targetEntity="App\Entity\SchoolLevelPhase", mappedBy="schoolPhase")
     *
     * @var Collection<array-key, SchoolLevelPhase>
     */
    private Collection $levelPhases;

    public function __construct()
    {
        $this->levelPhases = new ArrayCollection();
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

    /**
     * @return Collection<array-key, SchoolLevelPhase>
     */
    public function getLevelPhases(): Collection
    {
        return $this->levelPhases;
    }

    /**
     * @param Collection<array-key, SchoolLevelPhase> $levelPhases
     */
    public function setLevelPhases(Collection $levelPhases): void
    {
        $this->levelPhases = $levelPhases;
    }
}
