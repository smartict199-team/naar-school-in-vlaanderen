<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_school_form_types")
 */
class SchoolFormType
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
     * @ORM\OneToMany(targetEntity="App\Entity\SchoolLevelFormType", mappedBy="schoolFormType")
     *
     * @var Collection<array-key, SchoolLevelFormType>
     */
    private Collection $levelFormTypes;

    public function __construct()
    {
        $this->levelFormTypes = new ArrayCollection();
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
     * @return Collection<array-key, SchoolLevelFormType>
     */
    public function getLevelFormTypes(): Collection
    {
        return $this->levelFormTypes;
    }

    /**
     * @param Collection<array-key, SchoolLevelFormType> $levelFormTypes
     */
    public function setLevelFormTypes(Collection $levelFormTypes): void
    {
        $this->levelFormTypes = $levelFormTypes;
    }
}
