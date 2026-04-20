<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SchoolGradeRepository")
 * @ORM\Table(name="app_school_grades")
 */
class SchoolGrade
{
    public const BIRTH_YEAR_INTERNAL_NAME_PREFIX = 'birth_year_';
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
    private ?string $internalName = null;

    /**
     * @var Collection<array-key, SchoolYear>
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\SchoolYear", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="app_school_grades_school_years",
     *   joinColumns={@ORM\JoinColumn(name="grade_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="year_id", referencedColumnName="id")}
     * )
     */
    private Collection $years;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SchoolLevelGrade", mappedBy="schoolGrade", cascade={"persist"})
     *
     * @var Collection<array-key, SchoolLevelGrade>
     */
    private Collection $levelGrades;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $visibleFrontend = true;

    public function __construct()
    {
        $this->levelGrades = new ArrayCollection();
        $this->years = new ArrayCollection();
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
     * @return Collection<array-key, SchoolYear>
     */
    public function getYears(): Collection
    {
        return $this->years;
    }

    /**
     * @param Collection<array-key, SchoolYear> $years
     */
    public function setYears(Collection $years): void
    {
        $this->years = $years;
    }

    /**
     * @return Collection<array-key, SchoolLevelGrade>
     */
    public function getLevelGrades(): Collection
    {
        return $this->levelGrades;
    }

    /**
     * @param Collection<array-key, SchoolLevelGrade> $levelGrades
     */
    public function setLevelGrades(Collection $levelGrades): void
    {
        $this->levelGrades = $levelGrades;
    }

    public function getInternalName(): ?string
    {
        return $this->internalName;
    }

    public function setInternalName(?string $internalName): void
    {
        $this->internalName = $internalName;
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
