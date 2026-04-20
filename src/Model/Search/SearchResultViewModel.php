<?php

declare(strict_types=1);

namespace App\Model\Search;

use App\Entity\School;

class SearchResultViewModel
{
    private School $school;

    /**
     * @var SearchResultCapacityViewModel[]
     */
    private array $capacities;

    /**
     * @param SearchResultCapacityViewModel[] $capacities
     */
    public function __construct(School $school, array $capacities)
    {
        $this->school = $school;
        $this->capacities = $capacities;
    }

    public function getSchool(): School
    {
        return $this->school;
    }

    /**
     * @return SearchResultCapacityViewModel[]
     */
    public function getCapacities(): array
    {
        return $this->capacities;
    }

    public function addCapacity(SearchResultCapacityViewModel $capacity): void
    {
        $this->capacities[] = $capacity;

        usort($this->capacities, static function (SearchResultCapacityViewModel $a, SearchResultCapacityViewModel $b) {
            if ($a->getLevelPosition() === $b->getLevelPosition()) {
                return $a->getEducationPosition() <=> $b->getEducationPosition();
            }

            return $a->getLevelPosition() <=> $b->getLevelPosition();
        });
    }
}
