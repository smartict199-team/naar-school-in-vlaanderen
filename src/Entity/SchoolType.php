<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_school_types")
 */
class SchoolType
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
     * @ORM\OneToMany(targetEntity="App\Entity\SchoolLevelType", mappedBy="schoolType")
     *
     * @var Collection<array-key, SchoolLevelType>
     */
    private Collection $levelTypes;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $visibleFrontend = true;

    public function __construct()
    {
        $this->levelTypes = new ArrayCollection();
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
     * @return Collection<array-key, SchoolLevelType>
     */
    public function getLevelTypes(): Collection
    {
        return $this->levelTypes;
    }

    /**
     * @param Collection<array-key, SchoolLevelType> $levelTypes
     */
    public function setLevelTypes(Collection $levelTypes): void
    {
        $this->levelTypes = $levelTypes;
    }

    public function isVisibleFrontend(): bool
    {
        return $this->visibleFrontend;
    }

    public function setVisibleFrontend(bool $visibleFrontend): void
    {
        $this->visibleFrontend = $visibleFrontend;
    }
}
