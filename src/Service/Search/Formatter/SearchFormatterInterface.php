<?php

declare(strict_types=1);

namespace App\Service\Search\Formatter;

use App\Entity\SchoolEducation;
use App\Model\Search\SearchFilter;
use App\Model\Search\SearchResultCapacityViewModel;

interface SearchFormatterInterface
{
    public function format(SearchFilter $searchFilter, SchoolEducation $education): ?SearchResultCapacityViewModel;

    public function supports(SearchFilter $searchFilter, SchoolEducation $education): bool;
}
