<?php

declare(strict_types=1);

namespace App\Service\Transformer;

use App\Entity\SchoolGrade;
use App\Entity\SchoolLevel;
use App\Entity\SchoolYear;
use App\Model\Form\FilterForm;
use App\Repository\SchoolGradeRepository;
use App\Repository\SchoolLevelRepository;
use App\Repository\SchoolYearRepository;

class FilterFormModelTransformer
{
    private SchoolYearRepository $schoolYearRepository;
    private SchoolGradeRepository $schoolGradeRepository;
    private SchoolLevelRepository $schoolLevelRepository;

    public function __construct(
        SchoolYearRepository $schoolYearRepository,
        SchoolGradeRepository $schoolGradeRepository,
        SchoolLevelRepository $schoolLevelRepository
    ) {
        $this->schoolYearRepository = $schoolYearRepository;
        $this->schoolGradeRepository = $schoolGradeRepository;
        $this->schoolLevelRepository = $schoolLevelRepository;
    }

    /**
     * @param array{cities?: array, type?: null|string, level?: null|string, schoolYear?: int, schoolGrade?: int, schoolLevel?: int} $data
     */
    public function transformArrayDataToModel(array $data): FilterForm
    {
        $filterModel = new FilterForm();

        if (\array_key_exists('cities', $data)) {
            $filterModel->setCities($data['cities'] ?? []);
        }

        if (\array_key_exists('type', $data)) {
            $filterModel->setType($data['type']);
        }

        if (\array_key_exists('level', $data)) {
            $filterModel->setLevel($data['level']);
        }

        if (\array_key_exists('schoolYear', $data)) {
            $schoolYear = $this->schoolYearRepository->find($data['schoolYear']);

            if ($schoolYear instanceof SchoolYear) {
                $filterModel->setSchoolYear($schoolYear);
            }
        }

        if (\array_key_exists('schoolGrade', $data)) {
            $schoolGrade = $this->schoolGradeRepository->find($data['schoolGrade']);

            if ($schoolGrade instanceof SchoolGrade) {
                $filterModel->setSchoolGrade($schoolGrade);
            }
        }

        if (\array_key_exists('schoolLevel', $data)) {
            $schoolLevel = $this->schoolLevelRepository->find($data['schoolLevel']);

            if ($schoolLevel instanceof SchoolLevel) {
                $filterModel->setSchoolLevel($schoolLevel);
            }
        }

        return $filterModel;
    }
}
