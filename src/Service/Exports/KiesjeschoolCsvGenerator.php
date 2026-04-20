<?php

declare(strict_types=1);

namespace App\Service\Exports;

use App\Entity\SchoolLevel;
use App\Model\Export\Kiesjeschool;
use App\Model\Export\KiesjeschoolCapacity;
use League\Csv\Writer;

class KiesjeschoolCsvGenerator
{
    /**
     * @var string[]
     */
    public static array $headers = [
        'Naam vestigingsplaats',
        'Instellingsnummer',
        'Vestigingsnummer',
        'Hoofdgemeente',
        'Deelgemeente',
        'Postcode',
        'Adres',
        'Capaciteit Instapklas',
        'Zittende indicator leerlingen',
        'Zittende niet-indicator leerlingen',
        'Capaciteit 1e kleuterklas',
        'Zittende indicator leerlingen',
        'Zittende niet-indicator leerlingen',
        'Capaciteit 2e kleuterklas',
        'Zittende leerlingen',
        'Capaciteit 3e kleuterklas',
        'Zittende leerlingen',
        'Capaciteit 1e leerjaar',
        'Zittende indicator leerlingen',
        'Zittende niet-indicator leerlingen',
        'Capaciteit 2e leerjaar',
        'Zittende leerlingen',
        'Capaciteit 3e leerjaar',
        'Zittende leerlingen',
        'Capaciteit 4e leerjaar',
        'Zittende leerlingen',
        'Capaciteit 5e leerjaar',
        'Zittende leerlingen',
        'Capaciteit 6e leerjaar',
        'Zittende leerlingen',
        'Capaciteit 1e middelbaar',
        'Zittende indicator leerlingen',
        'Capaciteit 2e middelbaar',
        'Zittende indicator leerlingen',
        'Capaciteit 3e middelbaar',
        'Zittende indicator leerlingen',
        'Capaciteit 4e middelbaar',
        'Zittende indicator leerlingen',
        'Capaciteit 5e middelbaar',
        'Zittende indicator leerlingen',
        'Capaciteit 6e middelbaar',
        'Zittende indicator leerlingen',
    ];

    /**
     * @param list<Kiesjeschool> $data
     */
    public function generate(array $data): string
    {
        $csv = Writer::createFromString();
        $csv->setDelimiter(';');
        $csv->insertOne(self::$headers);

        foreach ($data as $item) {
            $row = $this->createRow($item);
            if (\count($row) !== \count(self::$headers)) {
                throw new \RuntimeException(sprintf('Incorrect number of columns in CSV. Expected %d, got %d', \count(self::$headers), \count($row)));
            }
            $csv->insertOne($row);
        }

        return $csv->getContent();
    }

    /**
     * @return array<int|string|null>
     */
    private function createRow(Kiesjeschool $data): array
    {
        $school = $data->getSchool();

        return [
            $school->getName(),
            implode(',', $school->getEstablishmentNumbers()),
            $school->getInstitutionNumber(),
            $school->getRegion(),
            $school->getCity(),
            $school->getPostalCode(),
            $school->getAddress(),

            $this->getCapacity($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 0),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 0),
            $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 0),

            $this->getCapacity($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 1),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 1),
            $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 1),

            $this->getCapacity($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 2),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 2)
              + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 2),

            $this->getCapacity($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 3),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 3)
              + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION, 3),

            $this->getCapacity($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 1),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 1),
            $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 1),

            $this->getCapacity($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 2),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 2)
              + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 2),

            $this->getCapacity($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 3),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 3)
              + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 3),

            $this->getCapacity($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 4),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 4)
              + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 4),

            $this->getCapacity($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 5),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 5)
              + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 5),

            $this->getCapacity($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 6),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 6)
              + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_PRIMARY_EDUCATION, 6),

            $this->getCapacity($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 1),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 1)
            + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 1),

            $this->getCapacity($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 2),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 2)
            + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 2),

            $this->getCapacity($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 3),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 3)
            + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 3),

            $this->getCapacity($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 4),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 4)
            + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 4),

            $this->getCapacity($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 5),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 5)
            + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 5),

            $this->getCapacity($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 6),
            $this->getIndicatorStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 6)
            + $this->getStudentSeatsTaken($data, SchoolLevel::TYPE_SECONDARY_EDUCATION, 6),
        ];
    }

    // @TODO These functions can be replaced with the nullsafe operator ?-> when the project is upgraded to PHP8

    private function getCapacity(Kiesjeschool $data, string $level, int $grade): ?int
    {
        $capacity = $data->getCapacity($level, $grade);

        if (!$capacity instanceof KiesjeschoolCapacity) {
            return null;
        }

        return $capacity->getCapacity();
    }

    private function getIndicatorStudentSeatsTaken(Kiesjeschool $data, string $level, int $grade): ?int
    {
        $capacity = $data->getCapacity($level, $grade);

        if (!$capacity instanceof KiesjeschoolCapacity) {
            return null;
        }

        return $capacity->getIndicatorStudentSeatsTaken();
    }

    private function getStudentSeatsTaken(Kiesjeschool $data, string $level, int $grade): ?int
    {
        $capacity = $data->getCapacity($level, $grade);

        if (!$capacity instanceof KiesjeschoolCapacity) {
            return null;
        }

        return $capacity->getStudentSeatsTaken();
    }
}
