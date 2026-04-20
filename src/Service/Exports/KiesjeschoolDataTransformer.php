<?php

declare(strict_types=1);

namespace App\Service\Exports;

use App\Entity\School;
use App\Entity\SchoolEducation;
use App\Entity\SchoolGrade;
use App\Entity\SchoolLevel;
use App\Entity\SchoolYear;
use App\Model\Export\Kiesjeschool;
use App\Model\Export\KiesjeschoolCapacity;

class KiesjeschoolDataTransformer
{
    public function transform(School $school, SchoolYear $schoolYear): ?Kiesjeschool
    {
        $data = new Kiesjeschool($school);
        $hasData = false;

        foreach ($school->getEducations() as $education) {
            if (!$education->getYear() instanceof SchoolYear
                || $education->getYear()->getId() !== $schoolYear->getId()
            ) {
                continue;
            }

            $newData = $this->transformEducation($education, $schoolYear);
            if (!$newData instanceof Kiesjeschool) {
                continue;
            }

            $data = $this->merge($data, $newData);
            $hasData = true;
        }

        return $hasData ? $data : null;
    }

    private function transformEducation(SchoolEducation $education, SchoolYear $schoolYear): ?Kiesjeschool
    {
        $school = $education->getSchool();
        if (!$school instanceof School) {
            return null;
        }

        $level = $this->getLevel($education);

        switch ($level) {
            case SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION:
                $grade = $this->getGradePrePrimary($education, $schoolYear);
                break;
            case SchoolLevel::TYPE_PRIMARY_EDUCATION:
                $grade = $this->getGradePrimary($education);
                break;
            case SchoolLevel::TYPE_SECONDARY_EDUCATION:
                $grade = $this->getGradeSecondary($education);
                break;
            default:
                return null;
        }

        if (null === $grade) {
            return null;
        }

        $capacity = $this->getCapacity($education);
        if (!$capacity instanceof KiesjeschoolCapacity) {
            return null;
        }

        $data = new Kiesjeschool($school);
        $data->setCapacity($level, $grade, $capacity);

        return $data;
    }

    private function getCapacity(SchoolEducation $education): ?KiesjeschoolCapacity
    {
        $capacity = $education->getCapacity();
        $indicatorStudentSeatsTaken = $education->getIndicatorStudentSeatsTaken();
        $studentSeatsTaken = $education->getStudentSeatsTaken();

        if (null === $capacity && null === $indicatorStudentSeatsTaken && null === $studentSeatsTaken) {
            return null;
        }

        $dataCapacity = new KiesjeschoolCapacity();
        $dataCapacity->setCapacity($capacity);
        $dataCapacity->setIndicatorStudentSeatsTaken($indicatorStudentSeatsTaken);
        $dataCapacity->setStudentSeatsTaken($studentSeatsTaken);

        return $dataCapacity;
    }

    private function getLevel(SchoolEducation $education): ?string
    {
        $schoolLevel = $education->getLevel();
        if (!$schoolLevel instanceof SchoolLevel) {
            return null;
        }

        if (SchoolLevel::LEVEL_REGULAR_EDUCATION !== $schoolLevel->getLevel()) {
            return null;
        }

        if (SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION !== $schoolLevel->getType()
            && SchoolLevel::TYPE_PRIMARY_EDUCATION !== $schoolLevel->getType()
            && SchoolLevel::TYPE_SECONDARY_EDUCATION !== $schoolLevel->getType()
        ) {
            return null;
        }

        return $schoolLevel->getType();
    }

    private function getGradePrePrimary(SchoolEducation $education, SchoolYear $year): ?int
    {
        $schoolGrade = $education->getGrade();
        if (!$schoolGrade instanceof SchoolGrade) {
            return null;
        }

        $birthYear = $this->getBirthYear($schoolGrade);
        if (null === $birthYear) {
            return null;
        }

        $grade = $year->getStartYear() - $birthYear - 2;

        if ($grade < 0 || $grade > 3) {
            throw new \RuntimeException(sprintf('Unexpected birthyear %d for schoolyear %d-%d', $birthYear, $year->getStartYear(), $year->getEndYear()));
        }

        return $grade;
    }

    private function getGradePrimary(SchoolEducation $education): ?int
    {
        $schoolGrade = $education->getGrade();
        if (!$schoolGrade instanceof SchoolGrade) {
            return null;
        }

        switch ($schoolGrade->getName()) {
            case '1ste leerjaar': return 1;
            case '2de leerjaar': return 2;
            case '3de leerjaar': return 3;
            case '4de leerjaar': return 4;
            case '5de leerjaar': return 5;
            case '6de leerjaar': return 6;
            case '7de leerjaar': return 7;
        }

        return null;
    }

    private function getGradeSecondary(SchoolEducation $education): ?int
    {

        $schoolGrade = $education->getLevel();

        if (!$schoolGrade instanceof SchoolLevel) {
            return null;
        }

        switch ($schoolGrade->getName()) {
            case '1ste middelbaar': return 1;
            case '2de middelbaar': return 2;
            case '3de middelbaar': return 3;
            case '4de middelbaar': return 4;
            case '5de middelbaar': return 5;
            case '6de middelbaar': return 6;
        }

        return null;
    }

    private function getBirthYear(SchoolGrade $schoolGrade): ?int
    {
        $internalName = $schoolGrade->getInternalName();

        if (empty($internalName)) {
            return null;
        }

        if (0 !== strpos($internalName, SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX)) {
            return null;
        }

        return (int) str_replace(SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX, '', $internalName);
    }

    private function merge(Kiesjeschool $existing, Kiesjeschool $new): Kiesjeschool
    {
        foreach ($new->getAllCapacities() as $level => $capacities) {
            foreach ($capacities as $grade => $newCapacity) {
                $existingCapacity = $existing->getCapacity($level, $grade);
                $capacity = $this->mergeCapacities($existingCapacity, $newCapacity);
                $existing->setCapacity($level, $grade, $capacity);
            }
        }

        return $existing;
    }

    private function mergeCapacities(?KiesjeschoolCapacity $a, ?KiesjeschoolCapacity $b): ?KiesjeschoolCapacity
    {
        if (null === $a) {
            return $b;
        }
        if (null === $b) {
            return $a;
        }

        $dataCapacity = new KiesjeschoolCapacity();

        if (null !== $a->getCapacity() || null !== $b->getCapacity()) {
            $dataCapacity->setCapacity(
                (int) $a->getCapacity() + (int) $b->getCapacity()
            );
        }
        if (null !== $a->getIndicatorStudentSeatsTaken() || null !== $b->getIndicatorStudentSeatsTaken()) {
            $dataCapacity->setIndicatorStudentSeatsTaken(
                (int) $a->getIndicatorStudentSeatsTaken() + (int) $b->getIndicatorStudentSeatsTaken()
            );
        }
        if (null !== $a->getStudentSeatsTaken() || null !== $b->getStudentSeatsTaken()) {
            $dataCapacity->setStudentSeatsTaken(
                (int) $a->getStudentSeatsTaken() + (int) $b->getStudentSeatsTaken()
            );
        }

        return $dataCapacity;
    }
}
