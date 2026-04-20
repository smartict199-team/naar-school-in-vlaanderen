<?php

declare(strict_types=1);

namespace App\Model\Api;

use App\Model\Api\LevelEducations\EducationNumbers;
use App\Model\Api\LevelEducations\Level;

class LevelEducations
{
    public Level $level;
    /** @var array<EducationNumbers> */
    public array $schoolNumbers = [];

    public function __construct()
    {
        $this->level = new Level();
    }
}
