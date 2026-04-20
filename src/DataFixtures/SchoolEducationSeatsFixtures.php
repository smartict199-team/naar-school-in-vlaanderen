<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SchoolEducation;
use App\Entity\SchoolYear;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SchoolEducationSeatsFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var array<string, array<int, string>>
     */
    private static array $sources = [
        'primary' => [
            2019 => './assets/fixtures/entries2019lager.data.json',
            2020 => './assets/fixtures/entries2020lager.data.json',
            2021 => './assets/fixtures/entries2021lager.data.json',
        ],
        'secondary' => [
            2019 => './assets/fixtures/entries2019secundair.data.json',
            2020 => './assets/fixtures/entries2020secundair.data.json',
            2021 => './assets/fixtures/entries2021secundair.data.json',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::$sources as $type => $typeSources) {
            foreach ($typeSources as $year => $source) {
                $json = file_get_contents($source);
                if (false === $json) {
                    throw new \RuntimeException(sprintf('Could not read url %s', $source));
                }

                $data = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
                $this->loadData($manager, $data, $type, $year);
            }
        }
    }

    public function getDependencies(): array
    {
        return [
            SchoolEducationsFixtures::class,
            SchoolYearsFixtures::class,
        ];
    }

    /**
     * @param array{
     *  id: int,
     *  opleiding_id: int,
     *  plaatsen:int,
     *  plaatsenbezet:int,
     *  plaatsenbezetind: int,
     *  percentageind:int,
     *  datum: string,
     *  volzet: null|string,
     *  plaatsenanderstalig: int,
     *  plaatsenanderstaligbezet: int,
     *  percentageindtonen: null|string,
     *  hide: null|string,
     * }[] $data
     */
    private function loadData(ObjectManager $manager, array $data, string $type, int $year): void
    {
        foreach ($data as $numbers) {
            if ('ja' === $numbers['hide']) {
                continue;
            }

            $educationRef = sprintf('education_%s_%s_%s', $type, $numbers['opleiding_id'], $year);
            if (!$this->hasReference($educationRef)) {
                continue;
            }

            $education = $this->getReference($educationRef);
            if (!$education instanceof SchoolEducation) {
                continue;
            }

            $education->setCapacity($numbers['plaatsen']);
            $education->setStudentSeatsTaken($numbers['plaatsenbezet']);
            $education->setIndicatorStudentSeatsTaken($numbers['plaatsenbezetind']);
            $education->setIndicatorStudentSeatsPercentage($numbers['percentageind']);
            $education->setIndicatorStudentSeatsPercentageVisible('ja' === $numbers['percentageindtonen']);

            $capacityReachedAtDate = $numbers['volzet'];
            if (null !== $capacityReachedAtDate && '' !== $capacityReachedAtDate && 'ja' !== $capacityReachedAtDate) {
                $capacityReachedAt = \DateTime::createFromFormat('d/m/Y H:i', $capacityReachedAtDate);
                if ($capacityReachedAt instanceof \DateTime) {
                    $education->setCapacityReached(true);
                    $education->setCapacityReachedAt($capacityReachedAt);
                }
            }

            $yearRef = sprintf('year_%s', (string) $year);
            if (!$this->hasReference($yearRef)) {
                continue;
            }

            $schoolYear = $this->getReference($yearRef);
            if (!$schoolYear instanceof SchoolYear) {
                continue;
            }

            $education->setYear($schoolYear);

            $manager->persist($education);
        }

        $manager->flush();
    }
}
