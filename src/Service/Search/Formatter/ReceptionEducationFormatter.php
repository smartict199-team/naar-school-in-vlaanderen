<?php

declare(strict_types=1);

namespace App\Service\Search\Formatter;

use App\Entity\SchoolEducation;
use App\Entity\SchoolLevel;
use App\Model\Search\SearchFilter;
use App\Model\Search\SearchResultCapacityViewModel;
use App\Repository\SchoolEducationRepository;
use App\Repository\SchoolLevelRepository;

class ReceptionEducationFormatter implements SearchFormatterInterface
{
    const BIRTH_YEAR_RANGE = 5;

    private SchoolEducationRepository $schoolEducationRepository;
    private SchoolLevelRepository $schoolLevelRepository;

    public function __construct(SchoolEducationRepository $schoolEducationRepository, SchoolLevelRepository $schoolLevelRepository)
    {
        $this->schoolEducationRepository = $schoolEducationRepository;
        $this->schoolLevelRepository = $schoolLevelRepository;
    }

    public function format(SearchFilter $searchFilter, SchoolEducation $education): ?SearchResultCapacityViewModel
    {
        $filterType = $searchFilter->getType();

        if (SchoolLevel::TYPE_SECONDARY_EDUCATION === $filterType) {
            $receptionLevel = $this->schoolLevelRepository->findOneBy(['type' => SchoolLevel::TYPE_SECONDARY_EDUCATION, 'level' => SchoolLevel::LEVEL_RECEPTION_EDUCATION]);
        } else {
            $receptionLevel = $this->schoolLevelRepository->findOneBy(['type' => SchoolLevel::TYPE_PRIMARY_EDUCATION, 'level' => SchoolLevel::LEVEL_RECEPTION_EDUCATION]);
        }

        if (!$this->isInBirthYearRange($education)) {
            return null;
        }

        $receptionEducation = $this->schoolEducationRepository->findOneBy(['school' => $education->getSchool(), 'year' => $searchFilter->getSchoolYear(), 'level' => $receptionLevel]);

        if (!$receptionEducation instanceof SchoolEducation) {
            return null;
        }

        $searchResult = new SearchResultCapacityViewModel();
        $searchResult->setTitle($receptionEducation->getFullName());
        if (SchoolLevel::TYPE_SECONDARY_EDUCATION !== $filterType) {
            $searchResult->setSubtitle($education->getFullName());
        }

        $receptionSeats = $receptionEducation->getRemainingSeats();
        $educationSeats = $education->getRemainingSeats();

        if (0 === $receptionSeats) {
            $searchResult->setCapacity($educationSeats);
            $searchResult->setUpdatedAt($education->getCapacityUpdatedAt());
        } elseif (0 === $educationSeats) {
            $searchResult->setCapacity($receptionSeats);
            $searchResult->setUpdatedAt($receptionEducation->getCapacityUpdatedAt());
        } elseif ($receptionSeats < $educationSeats) {
            $searchResult->setCapacity($receptionSeats);
            $searchResult->setUpdatedAt($receptionEducation->getCapacityUpdatedAt());
        } else {
            $searchResult->setCapacity($educationSeats);
            $searchResult->setUpdatedAt($education->getCapacityUpdatedAt());
        }

        if ($education->isCapacityReached()) {
            $searchResult->setCapacityReached(true);
            $searchResult->setUpdatedAt($education->getCapacityReachedAt());
        } elseif ($receptionEducation->isCapacityReached()) {
            $searchResult->setCapacityReached($receptionEducation->isCapacityReached());
            $searchResult->setUpdatedAt($receptionEducation->getCapacityReachedAt());
        }

        if (0 === $searchResult->getCapacity() && !$searchResult->isCapacityReached()) {
            $searchResult->setForcedAvailable(true);
        }

        $searchResult->setEducationPosition($education->getPosition());

        if ($receptionLevel instanceof SchoolLevel) {
            $searchResult->setLevelPosition((int) $receptionLevel->getPosition());
        }

        return $searchResult;
    }

    public function supports(SearchFilter $searchFilter, SchoolEducation $education): bool
    {
        return SchoolLevel::LEVEL_RECEPTION_EDUCATION === $searchFilter->getLevel();
    }

    protected function isInBirthYearRange(SchoolEducation $education): bool
    {
        if (null === $education->getYear()) {
            return true;
        }
        $startYear = $education->getYear()->getStartYear();

        if (null === $education->getGrade() || null === $education->getGrade()->getInternalName()) {
            return true;
        }
        preg_match('/^birth_year_(\d{4})$/', $education->getGrade()->getInternalName(), $matches);

        if (empty($matches[1])) {
            return true;
        }
        $birthYear = (int) $matches[1];

        return ($startYear - $birthYear) >= self::BIRTH_YEAR_RANGE;
    }
}
