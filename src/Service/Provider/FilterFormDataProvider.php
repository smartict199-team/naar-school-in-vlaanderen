<?php

declare(strict_types=1);

namespace App\Service\Provider;

use App\Entity\SchoolEducation;
use App\Entity\SchoolLevel;
use App\Entity\SchoolYear;
use App\Model\Form\FilterForm;
use App\Model\Form\SchoolCityChoice;
use App\Repository\SchoolEducationRepository;
use App\Repository\SchoolLevelRepository;
use App\Repository\SchoolRepository;
use App\Repository\SchoolYearRepository;
use App\Service\Search\FilterFormatter;

class FilterFormDataProvider
{
    private SchoolRepository $schoolRepository;
    private SchoolYearRepository $schoolYearRepository;
    private SchoolEducationRepository $schoolEducationRepository;
    private SchoolLevelRepository $schoolLevelRepository;

    private FilterFormatter $filterFormatter;

    public function __construct(
        SchoolRepository $schoolRepository,
        SchoolYearRepository $schoolYearRepository,
        SchoolEducationRepository $schoolEducationRepository,
        SchoolLevelRepository $schoolLevelRepository,
        FilterFormatter $filterFormatter
    ) {
        $this->schoolRepository = $schoolRepository;
        $this->schoolYearRepository = $schoolYearRepository;
        $this->schoolEducationRepository = $schoolEducationRepository;
        $this->schoolLevelRepository = $schoolLevelRepository;
        $this->filterFormatter = $filterFormatter;
    }

    /**
     * @return array<string, array<string, SchoolCityChoice>>
     */
    public function getCityFilterChoices(): array
    {
        $schools = $this->schoolRepository->findAll();
        $choices = [];

        foreach ($schools as $school) {
            $region = (string) $school->getRegion();
            $city = (string) $school->getCity();
            $postalCode = (string) $school->getPostalCode();
            if (!empty($region) && !empty($city) && !empty($postalCode)) {
                $cityName = sprintf('%s (%s)', $city, $postalCode);
                $choices[$region][$cityName] = new SchoolCityChoice($postalCode, $city, $region);
            }
        }

        return $this->orderCityFilterChoices($choices);
    }

    /**
     * @return SchoolYear[]
     */
    public function getSchoolYearFilterChoices(): array
    {
        return $this->schoolYearRepository->findActiveFrontendSchoolYears();
    }

    /**
     * @return SchoolLevel[]
     */
    public function getSecondaryGradeChoices(string $schoolType, string $schoolLevel): array
    {
        return $this->schoolLevelRepository->findBy(['type' => $schoolType, 'level' => $schoolLevel]);
    }

    /**
     * @return SchoolEducation[]
     */
    public function getEducations(FilterForm $filterForm): array
    {
        $filter = clone $filterForm;
        $this->normalizeCities($filter);

        $searchFilter = $this->filterFormatter->format($filter);

        return $this->schoolEducationRepository->findUniqueEducationsByFilter($searchFilter);
    }

    public function getLevel(string $level, string $type): ?SchoolLevel
    {
        return $this->schoolLevelRepository->findOneBy(['type' => $type, 'level' => $level]);
    }

    /**
     * @param array<string, array<string, SchoolCityChoice>> $cityFilterChoices
     *
     * @return array<string, array<string, SchoolCityChoice>>
     */
    private function orderCityFilterChoices(array $cityFilterChoices): array
    {
        ksort($cityFilterChoices);

        foreach ($cityFilterChoices as $region => &$cityChoices) {
            ksort($cityChoices);
        }

        return $cityFilterChoices;
    }

    private function normalizeCities(FilterForm $filter): void
    {
        $cities = array_values($this->getCityFilterChoices());
        $cityChoices = array_values(array_merge(...$cities));

        $cityChoicesWithKeys = [];
        foreach ($cityChoices as $city) {
            $key = sprintf(
                '%s-%s-%s',
                $city->getPostalCode(),
                $city->getregion() ?? '',
                $city->getCity()
            );
            $cityChoicesWithKeys[$key] = $city;
        }

        $filter->setCities(array_filter(
            array_map(
                static fn ($key) => $cityChoicesWithKeys[$key] ?? null,
                $filter->getCities()
            )
        ));
    }
}
