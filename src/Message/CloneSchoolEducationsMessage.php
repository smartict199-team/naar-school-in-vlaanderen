<?php

declare(strict_types=1);

namespace App\Message;

class CloneSchoolEducationsMessage
{
    private int $oldYearId;
    private int $newYearId;

    public function __construct(int $oldYearId, int $newYearId)
    {
        $this->oldYearId = $oldYearId;
        $this->newYearId = $newYearId;
    }

    public function getOldYearId(): int
    {
        return $this->oldYearId;
    }

    public function setOldYearId(int $oldYearId): void
    {
        $this->oldYearId = $oldYearId;
    }

    public function getNewYearId(): int
    {
        return $this->newYearId;
    }

    public function setNewYearId(int $newYearId): void
    {
        $this->newYearId = $newYearId;
    }
}
