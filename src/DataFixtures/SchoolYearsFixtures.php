<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SchoolYear;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SchoolYearsFixtures extends Fixture
{
    /**
     * @var array<int, bool[]>
     */
    public static array $schoolYears = [
        2019 => ['visibleInFrontend' => false, 'visibleInBackend' => false],
        2020 => ['visibleInFrontend' => true, 'visibleInBackend' => true],
        2021 => ['visibleInFrontend' => true, 'visibleInBackend' => true],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::$schoolYears as $year => $settings) {
            $schoolYear = new SchoolYear();
            $schoolYear->setStartYear($year);
            $schoolYear->setEndYear($year + 1);
            $schoolYear->setVisibleFrontend($settings['visibleInFrontend']);
            $schoolYear->setVisibleBackend($settings['visibleInBackend']);

            $this->setReference('year_' . $year, $schoolYear);
            $manager->persist($schoolYear);
        }

        $manager->flush();
    }
}
