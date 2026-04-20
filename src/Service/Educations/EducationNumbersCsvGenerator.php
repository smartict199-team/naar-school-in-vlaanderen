<?php

declare(strict_types=1);

namespace App\Service\Educations;

use App\Entity\School;
use App\Entity\SchoolEducation;
use App\Entity\SchoolFormType;
use App\Entity\SchoolGrade;
use App\Entity\SchoolLevel;
use App\Entity\SchoolPhase;
use App\Entity\SchoolType;
use App\Entity\SchoolYear;
use App\Model\Form\SchoolNumbersData;
use League\Csv\Writer;
use Symfony\Contracts\Translation\TranslatorInterface;

class EducationNumbersCsvGenerator
{
    /**
     * @var string[]
     */
    public static array $headers = [
        'School',
        'Schooljaar',
        'Type',
        'Vormtype',
        'Leerjaar',
        'Fase',
        'Onderwijstype',
        'Niveau',
        'Opleiding',
        'Categorie',
        'Administratieve groepen',
        'Percentage indicator',
        'Indicator aantal',
        'Capaciteit',
        'Plaatsen bezet',
        'Plaatsen bezet indicator',
        'Volzet',
        'Laatst aangepast',
    ];
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function generate(SchoolNumbersData $data): string
    {
        $csv = Writer::createFromString();
        $csv->setDelimiter(';');
        $csv->insertOne(self::$headers);

        foreach ($data->getEducationsCollections() as $schoolNumbersCollection) {
            foreach ($schoolNumbersCollection as $schoolNumbers) {
                $row = $this->createRow($schoolNumbers);
                if (\count($row) === \count(self::$headers)) {
                    $csv->insertOne($row);
                }
            }
        }

        return $csv->getContent();
    }

    /**
     * @return array<int|string|null>
     */
    private function createRow(SchoolEducation $schoolNumbers): array
    {
        $year = $schoolNumbers->getYear();
        $school = $schoolNumbers->getSchool();
        $level = $schoolNumbers->getLevel();
        if (!$year instanceof SchoolYear || !$school instanceof School || !$level instanceof SchoolLevel) {
            return [];
        }

        $capacityUpdatedAt = $schoolNumbers->getCapacityUpdatedAt();
        $capacityReachedAt = $schoolNumbers->getCapacityReachedAt();

        $grade = $schoolNumbers->getGrade();
        $type = $schoolNumbers->getType();
        $formType = $schoolNumbers->getFormType();
        $phase = $schoolNumbers->getPhase();

        return [
            $school->getName(),
            $year->getStartYear() . ' - ' . $year->getEndYear(),
            $type instanceof SchoolType ? $type->getName() : null,
            $formType instanceof SchoolFormType ? $formType->getName() : null,
            $grade instanceof SchoolGrade ? $grade->getName() : null,
            $phase instanceof SchoolPhase ? $phase->getName() : null,
            $this->translator->trans('app.admin.schools.school_level.levels.' . $level->getLevel()),
            $this->translator->trans('app.admin.schools.school_level.types.' . $level->getType()),
            $schoolNumbers->getName(),
            $level->getName(),
            $schoolNumbers->getAdministrativeGroups(),
            $schoolNumbers->getIndicatorStudentSeatsPercentage() . ($schoolNumbers->getIndicatorStudentSeatsPercentage() ? '%' : ''),
            $schoolNumbers->getIndicatorSeats(),
            $schoolNumbers->getCapacity(),
            $schoolNumbers->getStudentSeatsTaken(),
            $schoolNumbers->getIndicatorStudentSeatsTaken(),
            $capacityReachedAt instanceof \DateTime ? $capacityReachedAt->format('d/m/Y H:i:s') : null,
            $capacityUpdatedAt instanceof \DateTime ? $capacityUpdatedAt->format('d/m/Y H:i:s') : null,
        ];
    }
}
