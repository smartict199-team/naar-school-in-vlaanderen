<?php

declare(strict_types=1);

namespace App\Model\Search;

class SearchResultCapacityViewModel
{
    private ?string $title = null;

    private ?string $subtitle = null;

    private ?int $capacity = null;

    private ?float $indicatorStudentSeats = null;

    private bool $capacityReached = false;

    private bool $forcedAvailable = false;

    private ?\DateTime $updatedAt = null;

    private int $educationPosition = 0;

    private int $levelPosition = 0;

    private array $underrepresentedStudentSeats = [];

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): void
    {
        $this->subtitle = $subtitle;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): void
    {
        $this->capacity = $capacity;
    }

    public function getIndicatorStudentSeats(): ?float
    {
        return $this->indicatorStudentSeats;
    }

    public function setIndicatorStudentSeats(?float $indicatorStudentSeats): void
    {
        $this->indicatorStudentSeats = $indicatorStudentSeats;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function isCapacityReached(): bool
    {
        return $this->capacityReached;
    }

    public function setCapacityReached(bool $capacityReached): void
    {
        $this->capacityReached = $capacityReached;
    }

    public function isForcedAvailable(): bool
    {
        return $this->forcedAvailable;
    }

    public function setForcedAvailable(bool $forcedAvailable): void
    {
        $this->forcedAvailable = $forcedAvailable;
    }

    public function getEducationPosition(): int
    {
        return $this->educationPosition;
    }

    public function setEducationPosition(int $educationPosition): void
    {
        $this->educationPosition = $educationPosition;
    }

    public function getLevelPosition(): int
    {
        return $this->levelPosition;
    }

    public function setLevelPosition(int $levelPosition): void
    {
        $this->levelPosition = $levelPosition;
    }

    public function getUnderrepresentedStudentSeats(): array
    {
        return $this->underrepresentedStudentSeats;
    }

    public function setUnderrepresentedStudentSeats(array $underrepresentedStudentSeats): void
    {
        $this->underrepresentedStudentSeats = $underrepresentedStudentSeats;
    }

    public function addUnderrepresentedStudentSeat(int $seats, string $description): void
    {
        $this->underrepresentedStudentSeats[] = [
            'seats' => $seats,
            'description' => $description,
        ];
    }
}
