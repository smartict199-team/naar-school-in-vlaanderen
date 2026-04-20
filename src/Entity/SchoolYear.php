<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SchoolYearRepository")
 * @ORM\Table(name="app_school_years")
 */
class SchoolYear
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $startYear = 0;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $endYear = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $visibleBackend = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $visibleFrontend = false;

    public function __construct()
    {
        $date = new \DateTimeImmutable();
        $this->startYear = (int) $date->format('Y');
        $this->endYear = (int) $date->add(new \DateInterval('P1Y'))->format('Y');
    }

    public function __toString(): string
    {
        return sprintf('%s - %s', $this->getStartYear(), $this->getEndYear());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getStartYear(): int
    {
        return $this->startYear;
    }

    public function setStartYear(int $startYear): void
    {
        $this->startYear = $startYear;
    }

    public function getEndYear(): int
    {
        return $this->endYear;
    }

    public function setEndYear(int $endYear): void
    {
        $this->endYear = $endYear;
    }

    public function isVisibleBackend(): bool
    {
        return $this->visibleBackend;
    }

    public function setVisibleBackend(bool $visibleBackend): void
    {
        $this->visibleBackend = $visibleBackend;
    }

    public function isVisibleFrontend(): bool
    {
        return $this->visibleFrontend;
    }

    public function setVisibleFrontend(bool $visibleFrontend): void
    {
        $this->visibleFrontend = $visibleFrontend;
    }

    public function isCurrent(): bool
    {
        $currentDate = new \DateTime();
        $schoolYearStartDate = \DateTime::createFromFormat('Y-m-d', sprintf('%s-09-01', $this->startYear));
        $schoolYearEndDate = \DateTime::createFromFormat('Y-m-d', sprintf('%s-08-31', $this->endYear));

        return $currentDate >= $schoolYearStartDate && $currentDate <= $schoolYearEndDate;
    }
}
