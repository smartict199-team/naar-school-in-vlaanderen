<?php

declare(strict_types=1);

namespace App\Service\Educations;

use App\Entity\School;
use App\Entity\SchoolYear;
use App\Model\Form\SchoolEducationsData;
use App\Model\Form\SchoolNumbersData;
use App\Repository\SchoolLevelRepository;
use App\Repository\SchoolRepository;
use Doctrine\ORM\EntityManagerInterface;

class EducationsService
{
    private SchoolLevelRepository $levelRepository;
    private SchoolRepository $schoolRepository;
    private EntityManagerInterface $schoolEducationRepository;

    public function __construct(
        SchoolLevelRepository $levelRepository,
        SchoolRepository $schoolRepository,
        EntityManagerInterface $schoolEducationRepository
    ) {
        $this->levelRepository = $levelRepository;
        $this->schoolRepository = $schoolRepository;
        $this->schoolEducationRepository = $schoolEducationRepository;
    }

    public function saveEducations(School $school, SchoolYear $year, SchoolEducationsData $levelEducations): void
    {
        $schoolEducations = $school->getEducations();

        foreach ($schoolEducations as $schoolEducation) {
            if ($schoolEducation->getYear() === $year) {
                $school->removeEducation($schoolEducation);
            }
        }

        foreach ($levelEducations->getEducationsCollections() as $levelId => $educations) {
            $level = $this->levelRepository->find($levelId);
            foreach ($educations as $education) {
                $education->setSchool($school);
                $education->setYear($year);
                $education->setLevel($level);
                $education->setFormTypeVisibleOnFrontend($levelEducations->isFormTypeVisibleOnFrontend());
                $education->setFinalityVisibleOnFrontend($levelEducations->isFinalityVisibleOnFrontend());

                if (!$schoolEducations->contains($education)) {
                    $schoolEducations->add($education);
                }
            }
        }

        $school->setEducations($schoolEducations);
        $this->schoolRepository->save($school);
    }

    public function saveNumbers(SchoolNumbersData $schoolNumbers): void
    {
        foreach ($schoolNumbers->getEducationsCollections() as $collection) {
            foreach ($collection as $schoolEducation) {
                if (false === $schoolEducation->isCapacityReached()) {
                    $schoolEducation->setCapacityReachedAt(null);
                }

                $totalUnderrepresentedGroupsPercentage = 0;
                $totalUnderrepresentedGroupsSeatsTaken = 0;

                foreach ($schoolEducation->getUnderrepresentedGroups() as $underrepresentedGroup) {
                    $underrepresentedGroup->setSchoolEducation($schoolEducation);
                    $totalUnderrepresentedGroupsPercentage += $underrepresentedGroup->getStudentSeatsPercentage();
                    $totalUnderrepresentedGroupsSeatsTaken += $underrepresentedGroup->getStudentSeatsTaken();

                    $underrepresentedGroup->setCapacity((int) round($schoolEducation->getCapacity() * $underrepresentedGroup->getStudentSeatsPercentage() / 100));
                }

                if ($totalUnderrepresentedGroupsPercentage > 0) {
                    $schoolEducation->setIndicatorStudentSeatsPercentage($totalUnderrepresentedGroupsPercentage);
                    $schoolEducation->setIndicatorStudentSeatsTaken($totalUnderrepresentedGroupsSeatsTaken);
                }

                $this->schoolEducationRepository->persist($schoolEducation);
            }
        }

        $this->schoolEducationRepository->flush();
    }
}
