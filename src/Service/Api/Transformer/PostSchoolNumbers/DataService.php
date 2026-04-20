<?php

declare(strict_types=1);

namespace App\Service\Api\Transformer\PostSchoolNumbers;

use App\Entity\SchoolEducation;
use App\Entity\SchoolEducationUnderrepresentedGroup;
use App\Model\Api\Request\PostSchoolNumbersRequest;
use App\Model\Form\AbstractEducationsData;
use App\Model\Form\SchoolEducationsData;

class DataService
{
    public function enrichData(AbstractEducationsData $educationsData, PostSchoolNumbersRequest $postSchoolNumbersRequest): SchoolEducationsData
    {
        $educations = $educationsData->getEducationsCollections();

        foreach ($postSchoolNumbersRequest->educationNumbers as $educationNumbers) {
            $education = $this->getEducation($educations, $educationNumbers->educationId);

            if (!$education instanceof SchoolEducation) {
                continue;
            }

            if (null !== $educationNumbers->capacity) {
                $education->setCapacity($educationNumbers->capacity);
            }

            if (null !== $educationNumbers->indicatorStudentSeatsPercentage) {
                $education->setIndicatorStudentSeatsPercentage($educationNumbers->indicatorStudentSeatsPercentage);
            }
            if (null !== $educationNumbers->indicatorStudentSeatsTaken) {
                $education->setIndicatorStudentSeatsTaken($educationNumbers->indicatorStudentSeatsTaken);
            }
            if (null !== $educationNumbers->studentSeatsTaken) {
                $education->setStudentSeatsTaken($educationNumbers->studentSeatsTaken);
            }
            if (null !== $educationNumbers->indicatorStudentSeatsPercentageVisible) {
                $education->setIndicatorStudentSeatsPercentageVisible($educationNumbers->indicatorStudentSeatsPercentageVisible);
            }

            if ($educationNumbers->capacityReached) {
                $education->setCapacityReached(true);
                $capacityReachedAt = null !== $educationNumbers->capacityReachedAt ? \DateTime::createFromFormat(\DateTime::ATOM, $educationNumbers->capacityReachedAt) : new \DateTime();
                $education->setCapacityReachedAt($capacityReachedAt);
            }

            foreach ($educationNumbers->underrepresentedGroupNumbers as $underrepresentedGroupNumbers) {
                if (true === $underrepresentedGroupNumbers->deleted) {
                    $education->removeUnderrepresentedGroup($underrepresentedGroupNumbers->id);

                    continue;
                }
                if (null === $underrepresentedGroupNumbers->id) {
                    $underrepresentedGroup = new SchoolEducationUnderrepresentedGroup();
                    $education->addUnderrepresentedGroup($underrepresentedGroup);
                } else {
                    $underrepresentedGroup = $education->getUnderrepresentedGroup($underrepresentedGroupNumbers->id);
                }

                if (null !== $underrepresentedGroupNumbers->name) {
                    $underrepresentedGroup->setDescription($underrepresentedGroupNumbers->name);
                }

                if (null !== $underrepresentedGroupNumbers->studentSeatsTaken) {
                    $underrepresentedGroup->setStudentSeatsTaken($underrepresentedGroupNumbers->studentSeatsTaken);
                }

                if (null !== $underrepresentedGroupNumbers->studentSeatsPercentage) {
                    $underrepresentedGroup->setStudentSeatsPercentage($underrepresentedGroupNumbers->studentSeatsPercentage);
                }
            }
        }

        return $educationsData;
    }

    private function getEducation(array $educationCollections, int $id): ?SchoolEducation
    {
        foreach ($educationCollections as $educationCollection) {
            /** @var SchoolEducation $education */
            foreach ($educationCollection as $education) {
                if ($education->getId() === $id) {
                    return $education;
                }
            }
        }

        return null;
    }
}
