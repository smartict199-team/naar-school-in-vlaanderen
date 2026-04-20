<?php

declare(strict_types=1);

namespace App\Model\Export;

class KiesjeschoolCapacity
{
    private ?int $capacity = null;
    private ?int $indicatorStudentSeatsTaken = null;
    private ?int $studentSeatsTaken = null;

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): void
    {
        $this->capacity = $capacity;
    }

    public function getIndicatorStudentSeatsTaken(): ?int
    {
        return $this->indicatorStudentSeatsTaken;
    }

    public function setIndicatorStudentSeatsTaken(?int $indicatorStudentSeatsTaken): void
    {
        $this->indicatorStudentSeatsTaken = $indicatorStudentSeatsTaken;
    }

    public function getStudentSeatsTaken(): ?int
    {
        return $this->studentSeatsTaken;
    }

    public function setStudentSeatsTaken(?int $studentSeatsTaken): void
    {
        $this->studentSeatsTaken = $studentSeatsTaken;
    }
}
