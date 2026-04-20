<?php

declare(strict_types=1);

namespace App\Service\Educations;

use App\Entity\SchoolEducation;
use App\Entity\SchoolGrade;
use App\Entity\SchoolYear;
use App\Repository\SchoolGradeRepository;

class EducationCloner
{
    private SchoolGradeRepository $schoolGradeRepository;

    public function __construct(SchoolGradeRepository $schoolGradeRepository)
    {
        $this->schoolGradeRepository = $schoolGradeRepository;
    }

    public function clone(SchoolEducation $education, SchoolYear $newYear): SchoolEducation
    {
        $newEducation = clone $education;
        $newEducation->setYear($newYear);
        $newEducation->setCapacity(null);
        $newEducation->setCapacityReached(false);
        $newEducation->setCapacityReachedAt(null);
        $newEducation->setIndicatorStudentSeatsTaken(null);
        $newEducation->setIndicatorStudentSeatsPercentage(null);
        $newEducation->setCapacityUpdatedAt(null);
        $newEducation->setStudentSeatsTaken(null);
        $newEducation->setGrade($this->getNextGrade($education));

        return $newEducation;
    }

    private function getNextGrade(SchoolEducation $schoolEducation): ?SchoolGrade
    {
        $grade = $schoolEducation->getGrade();
        if (null === $grade) {
            return null;
        }

        $internalName = $grade->getInternalName();
        if (null === $internalName || false === strpos($internalName, SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX)) {
            return $grade;
        }

        $nextGradeYear = (int) str_replace(SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX, '', $internalName) + 1;

        return $this->schoolGradeRepository->findOneByInternalName(SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX . $nextGradeYear);
    }
}
