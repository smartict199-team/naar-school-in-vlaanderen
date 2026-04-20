<?php

declare(strict_types=1);

namespace App\Service\Api\Transformer;

use App\Entity\School;
use App\Entity\SchoolEducationUnderrepresentedGroup;
use App\Entity\SchoolGrade;
use App\Entity\SchoolPhase;
use App\Entity\SchoolYear;
use App\Model\Api\LevelEducations;
use App\Model\Api\LevelEducations\EducationNumbers;
use App\Model\Api\LevelEducations\SchoolNumbers\UnderrepresentedGroupNumbers;
use App\Model\Api\SchoolNumberResponse;
use App\Model\Form\AbstractEducationsData;
use App\Repository\SchoolLevelRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApiViewTransformer
{
    private TranslatorInterface $translator;
    private SchoolLevelRepository $levelRepository;

    public function __construct(
        TranslatorInterface $translator,
        SchoolLevelRepository $levelRepository
    ) {
        $this->translator = $translator;
        $this->levelRepository = $levelRepository;
    }

    public function getView(School $school, SchoolYear $schoolYear, AbstractEducationsData $educationsData): SchoolNumberResponse
    {
        $response = new SchoolNumberResponse();
        $response->id = $school->getId();
        $response->establishmentNumbers = $school->getEstablishmentNumbers();
        $response->schoolName = $school->getName();
        $response->startYear = $schoolYear->getStartYear();
        $response->endYear = $schoolYear->getEndYear();

        foreach ($educationsData->getEducationsCollections() as $levelId => $educationCollection) {
            $levelEducations = new LevelEducations();
            $levelEducations->level->id = $levelId;
            $level = $this->levelRepository->find($levelId);
            $levelEducations->level->levelLabel = $this->translator->trans('app.admin.schools.school_level.levels.' . $level->getLevel());
            $levelEducations->level->levelTypeLabel = $this->translator->trans('app.admin.schools.school_level.types.' . $level->getType());

            $response->levelEducations[] = $levelEducations;

            foreach ($educationCollection as $education) {
                $grade = $education->getGrade();
                $phase = $education->getPhase();

                $schoolNumbers = new EducationNumbers();
                $schoolNumbers->grade->id = $grade instanceof SchoolGrade ? $grade->getId() : null;
                $schoolNumbers->grade->label = $grade instanceof SchoolGrade ? $grade->getName() : null;

                $schoolNumbers->phase->id = $phase instanceof SchoolPhase ? $phase->getId() : null;
                $schoolNumbers->phase->label = $phase instanceof SchoolPhase ? $phase->getName() : null;

                $schoolNumbers->id = $education->getId();
                $schoolNumbers->label = $education->getName();
                $schoolNumbers->administrativeGroup = $education->getAdministrativeGroups();
                $schoolNumbers->capacity = $education->getCapacity();
                $schoolNumbers->indicatorStudentSeatsPercentage = $education->getIndicatorStudentSeatsPercentage();
                $schoolNumbers->indicatorStudentSeatsPercentageVisible = $education->isIndicatorStudentSeatsPercentageVisible();
                $schoolNumbers->indicatorStudentSeatsTaken = $education->getIndicatorStudentSeatsTaken();
                $schoolNumbers->studentSeatsTaken = $education->getStudentSeatsTaken();
                $schoolNumbers->capacityReached = $education->isCapacityReached();
                $schoolNumbers->capacityReachedAt = $education->getCapacityReachedAt() instanceof \DateTimeInterface ? $education->getCapacityReachedAt()->format(\DateTime::ATOM) : null;
                $schoolNumbers->capacityUpdatedAt = $education->getCapacityUpdatedAt() instanceof \DateTimeInterface ? $education->getCapacityUpdatedAt()->format(\DateTime::ATOM) : null;

                /** @var SchoolEducationUnderrepresentedGroup $underrepresentedGroup */
                foreach ($education->getUnderrepresentedGroups() as $underrepresentedGroup) {
                    $underrepresentedGroupNumbers = new UnderrepresentedGroupNumbers();
                    $underrepresentedGroupNumbers->id = $underrepresentedGroup->getId();
                    $underrepresentedGroupNumbers->name = $underrepresentedGroup->getDescription();
                    $underrepresentedGroupNumbers->capacity = $underrepresentedGroup->getCapacity();
                    $underrepresentedGroupNumbers->studentSeatsTaken = $underrepresentedGroup->getStudentSeatsTaken();
                    $underrepresentedGroupNumbers->studentSeatsPercentage = $underrepresentedGroup->getStudentSeatsPercentage();

                    $schoolNumbers->underrepresentedGroupNumbers[] = $underrepresentedGroupNumbers;
                }
                $levelEducations->schoolNumbers[] = $schoolNumbers;
            }
        }

        return $response;
    }
}
