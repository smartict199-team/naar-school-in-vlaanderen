<?php

declare(strict_types=1);

namespace App\Service\Search;

use App\Model\Form\FilterForm;
use App\Model\Search\SearchResultViewModel;
use App\Repository\SchoolEducationRepository;

class SearchService
{
    private SchoolEducationRepository $schoolEducationRepository;
    private SearchResultFormatter $searchFormatter;
    private FilterFormatter $filterFormatter;

    public function __construct(
        SchoolEducationRepository $schoolEducationRepository,
        SearchResultFormatter $searchFormatter,
        FilterFormatter $filterFormatter
    ) {
        $this->schoolEducationRepository = $schoolEducationRepository;
        $this->searchFormatter = $searchFormatter;
        $this->filterFormatter = $filterFormatter;
    }

    /**
     * @return array<int|string, array<int, SearchResultViewModel>>
     */
    public function search(FilterForm $filterForm): array
    {
        $searchFilter = $this->filterFormatter->format($filterForm);
        $educations = $this->schoolEducationRepository->findByFilter($searchFilter);

        return $this->searchFormatter->format($searchFilter, $educations);
    }
}
