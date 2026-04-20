<?php

declare(strict_types=1);

namespace App\Service\Search\Formatter;

use App\Entity\SchoolEducation;
use App\Entity\SchoolLevel;
use App\Model\Search\SearchFilter;
use App\Model\Search\SearchResultCapacityViewModel;

class SearchFormatter implements SearchFormatterInterface
{
    public function format(SearchFilter $searchFilter, SchoolEducation $education): ?SearchResultCapacityViewModel
    {
        $level = $education->getLevel();
        $result = new SearchResultCapacityViewModel();

        if (!$level instanceof SchoolLevel) {
            return null;
        }

        switch ($level->getType()) {
            case SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION:
            case SchoolLevel::TYPE_PRIMARY_EDUCATION:
                $result->setTitle($education->getSubTitle(true));

                break;
            case SchoolLevel::TYPE_SECONDARY_EDUCATION:
                $result->setTitle($education->getName());
                $result->setSubtitle($education->getSubTitle(true));

                if (SchoolLevel::INTERNAL_NAME_SECONDARY_REGULAR_EDUCATION_FIRST_GRADE !== $level->getInternalName() && SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION !== $level->getLevel()) {
                    $result->setForcedAvailable(true);
                }

                break;
            default:
        }

        $result->setCapacity($education->getRemainingSeats());
        $result->setCapacityReached($education->isCapacityReached());
        $result->setUpdatedAt($education->getCapacityUpdatedAt());
        $result->setEducationPosition($education->getPosition());
        $result->setLevelPosition((int) $level->getPosition());

        $indicatorPercentage = $education->getIndicatorStudentSeatsPercentage();

        if (0 != $indicatorPercentage && $education->isIndicatorStudentSeatsPercentageVisible()) {
            $result->setIndicatorStudentSeats($education->getRemainingIndicatorSeats());

            foreach ($education->getUnderrepresentedGroups() as $underrepresentedGroup) {
                $result->addUnderrepresentedStudentSeat($underrepresentedGroup->getCapacity(), $underrepresentedGroup->getDescription());
            }
        }

        if ($education->isCapacityReached()) {
            $result->setUpdatedAt($education->getCapacityReachedAt());
        }

        return $result;
    }

    public function supports(SearchFilter $searchFilter, SchoolEducation $education): bool
    {
        return SchoolLevel::LEVEL_RECEPTION_EDUCATION !== $searchFilter->getLevel();
    }
}
