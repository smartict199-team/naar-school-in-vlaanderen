<?php

declare(strict_types=1);

namespace App\Service\Search;

use App\Entity\School;
use App\Entity\SchoolEducation;
use App\Model\Search\SearchFilter;
use App\Model\Search\SearchResultCapacityViewModel;
use App\Model\Search\SearchResultViewModel;
use App\Service\Search\Formatter\SearchFormatterInterface;

class SearchResultFormatter
{
    /**
     * @var SearchFormatterInterface[]
     */
    private iterable $searchFormatters;

    /**
     * @param SearchFormatterInterface[] $searchFormatters
     */
    public function __construct(iterable $searchFormatters)
    {
        $this->searchFormatters = $searchFormatters;
    }

    /**
     * @param SchoolEducation[] $educations
     *
     * @return array<int|string, array<int, SearchResultViewModel>>
     */
    public function format(SearchFilter $searchFilter, array $educations): array
    {
        $results = [];

        foreach ($educations as $education) {
            $result = $this->getFormattedResult($searchFilter, $education);
            $school = $education->getSchool();

            if (!$school instanceof School) {
                continue;
            }

            if (!$result instanceof SearchResultCapacityViewModel) {
                continue;
            }

            $results = $this->addResult($result, $school, $results);
        }

        return $results;
    }

    private function getFormattedResult(SearchFilter $searchFilter, SchoolEducation $education): ?SearchResultCapacityViewModel
    {
        foreach ($this->searchFormatters as $searchFormatter) {
            if (!$searchFormatter->supports($searchFilter, $education)) {
                continue;
            }

            return $searchFormatter->format($searchFilter, $education);
        }

        return null;
    }

    /**
     * @param array<int|string, array<int, SearchResultViewModel>> $results
     *
     * @return array<int|string, array<int, SearchResultViewModel>>
     */
    private function addResult(SearchResultCapacityViewModel $newResult, School $school, array $results): array
    {
        foreach ($results as $schoolsResults) {
            foreach ($schoolsResults as $result) {
                if ($result->getSchool() === $school) {
                    $result->addCapacity($newResult);

                    return $results;
                }
            }
        }

        $establishmentKey = implode('', $school->getEstablishmentNumbers());

        $results[$establishmentKey][] = new SearchResultViewModel($school, [$newResult]);
        shuffle($results[$establishmentKey]);

        return $results;
    }
}
