<?php

declare(strict_types=1);

namespace App\Service\Educations;

use App\Entity\School;
use App\Entity\SchoolEducation;
use App\Entity\SchoolLevel;
use App\Entity\SchoolYear;
use App\Repository\SchoolLevelRepository;

class DefaultEducationBuilder
{
    private SchoolLevelRepository $schoolLevelRepository;

    public function __construct(SchoolLevelRepository $schoolLevelRepository)
    {
        $this->schoolLevelRepository = $schoolLevelRepository;
    }

    /**
     * @return array<array-key, SchoolEducation>
     */
    public function build(School $school, SchoolYear $schoolYear): array
    {
        $levels = $this->schoolLevelRepository->findByLevelsTypes(
            $school->getSchoolLevelLevels(),
            $school->getSchoolLevelTypes()
        );

        $defaultEducations = [];

        foreach ($levels as $level) {
            $levelId = $level->getId();
            if (null === $levelId) {
                continue;
            }

            $educations = $school->getEducationsForLevelAndYear($level, $schoolYear);
            if ($educations->count() <= 0 && $level->isDefaultRequired()) {
                $education = $this->createDefaultEducation($school, $level, $schoolYear);

                $defaultEducations[] = $education;
            }
        }

        return $defaultEducations;
    }

    private function createDefaultEducation(School $school, SchoolLevel $level, SchoolYear $schoolYear): SchoolEducation
    {
        $education = new SchoolEducation();
        $education->setSchool($school);
        $education->setLevel($level);
        $education->setYear($schoolYear);
        $education->setGrade($level->getDefaultGrade());
        $education->setType($level->getDefaultType());
        $education->setDefault(true);

        return $education;
    }
}
