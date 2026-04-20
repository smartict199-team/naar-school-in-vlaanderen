<?php

declare(strict_types=1);

namespace App\Service\Educations;

use App\Entity\School;
use App\Entity\SchoolEducation;
use App\Entity\SchoolYear;
use App\Model\Form\AbstractEducationsData;
use App\Model\Form\SchoolEducationsData;

class EducationsFormDataTransformer
{
    private DefaultEducationBuilder $defaultEducationBuilder;

    public function __construct(DefaultEducationBuilder $defaultEducationBuilder)
    {
        $this->defaultEducationBuilder = $defaultEducationBuilder;
    }

    /**
     * @param class-string<AbstractEducationsData> $class
     */
    public function transform(School $school, SchoolYear $schoolYear, string $class = SchoolEducationsData::class): AbstractEducationsData
    {
        $formTypeVisibleOnFrontend = null;
        $finalityVisibleOnFrontend = null;

        $levelEducations = [];
        foreach ($school->getEducations() as $education) {
            $level = $education->getLevel();
            if (null !== $level && $schoolYear === $education->getYear()) {
                $levelId = $level->getId();
                if (null !== $levelId) {
                    $levelEducations[$levelId][] = $education;

                    if (null === $formTypeVisibleOnFrontend && $level->isFormTypeRequired()) {
                        $formTypeVisibleOnFrontend = $education->isFormTypeVisibleOnFrontend();
                    }

                    if (null === $finalityVisibleOnFrontend && $level->isFinalityRequired()) {
                        $finalityVisibleOnFrontend = $education->isFinalityVisibleOnFrontend();
                    }
                }
            }
        }

        $levelEducations = $this->addDefaults($school, $schoolYear, $levelEducations);

        uasort($levelEducations, static function (array $a, array $b) {
            $arrayKeyA = array_key_first($a);
            $arrayKeyB = array_key_first($b);
            if (null === $arrayKeyA || null === $arrayKeyB) {
                return 0;
            }

            $firstEducationA = $a[$arrayKeyA];
            $firstEducationB = $b[$arrayKeyB];
            if (!$firstEducationA instanceof SchoolEducation || !$firstEducationB instanceof SchoolEducation) {
                return 0;
            }

            $levelA = $firstEducationA->getlevel();
            $levelB = $firstEducationB->getlevel();
            if (null === $levelA || null === $levelB) {
                return 0;
            }

            return $levelA->getPosition() <=> $levelB->getPosition();
        });

        $dataClass = new $class();
        $dataClass->setEducationsCollections($levelEducations);
        if ($dataClass instanceof SchoolEducationsData && null !== $formTypeVisibleOnFrontend) {
            $dataClass->setFormTypeVisibleOnFrontend($formTypeVisibleOnFrontend);
        }

        if ($dataClass instanceof SchoolEducationsData && null !== $finalityVisibleOnFrontend) {
            $dataClass->setFinalityVisibleOnFrontend($finalityVisibleOnFrontend);
        }

        return $dataClass;
    }

    /**
     * @param array<int, SchoolEducation[]> $levelEducations
     *
     * @return array<int, SchoolEducation[]>
     */
    private function addDefaults(School $school, SchoolYear $schoolYear, array $levelEducations): array
    {
        $educations = $this->defaultEducationBuilder->build($school, $schoolYear);
        foreach ($educations as $education) {
            if (false === $education->isDeletable()) {
                $levelEducations[(int) $education->getLevel()->getId()] = [$education];
            }
        }

        return $levelEducations;
    }
}
