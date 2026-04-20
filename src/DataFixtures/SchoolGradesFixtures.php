<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SchoolGrade;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

class SchoolGradesFixtures extends Fixture
{
    /**
     * @var array<string, array{name: string, internalName?: string, yearRefs: string[], visibleFrontend?: bool}>
     */
    public static array $grades = [
        'grade_1' => ['name' => '1ste leerjaar', 'yearRefs' => []],
        'grade_2' => ['name' => '2de leerjaar', 'yearRefs' => []],
        'grade_3' => ['name' => '3de leerjaar', 'yearRefs' => []],
        'grade_4' => ['name' => '4de leerjaar', 'yearRefs' => []],
        'grade_5' => ['name' => '5de leerjaar', 'yearRefs' => []],
        'grade_6' => ['name' => '6de leerjaar', 'yearRefs' => []],
        'grade_7' => ['name' => '7de leerjaar', 'yearRefs' => []],
        'grade_birth_year_2014' => ['name' => 'Geboortejaar 2014', 'internalName' => SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX . '2014', 'yearRefs' => ['year_2019']],
        'grade_birth_year_2015' => ['name' => 'Geboortejaar 2015', 'internalName' => SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX . '2015', 'yearRefs' => ['year_2019', 'year_2020']],
        'grade_birth_year_2016' => ['name' => 'Geboortejaar 2016', 'internalName' => SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX . '2016', 'yearRefs' => ['year_2019', 'year_2020', 'year_2021']],
        'grade_birth_year_2017' => ['name' => 'Geboortejaar 2017', 'internalName' => SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX . '2017', 'yearRefs' => ['year_2019', 'year_2020', 'year_2021']],
        'grade_birth_year_2018' => ['name' => 'Geboortejaar 2018', 'internalName' => SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX . '2018', 'yearRefs' => ['year_2020', 'year_2021']],
        'grade_birth_year_2019' => ['name' => 'Geboortejaar 2019', 'internalName' => SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX . '2019', 'yearRefs' => ['year_2021']],
        'grade_pre_primary_education' => ['name' => 'Kleuter', 'yearRefs' => ['year_2019', 'year_2020', 'year_2021'], 'visibleFrontend' => false],
        'grade_primary_education' => ['name' => 'Lager', 'yearRefs' => [], 'visibleFrontend' => false],
        'grade_reception_education' => ['name' => 'Onthaalonderwijs', 'yearRefs' => [], 'visibleFrontend' => false],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::$grades as $reference => $grade) {
            $schoolGrade = new SchoolGrade();
            $schoolGrade->setName($grade['name']);
            $schoolGrade->setInternalName($grade['internalName'] ?? null);

            $years = [];
            foreach ($grade['yearRefs'] as $yearRef) {
                $years[] = $this->getReference($yearRef);
            }

            $schoolGrade->setYears(new ArrayCollection($years));

            if (\array_key_exists('visibleFrontend', $grade)) {
                $schoolGrade->setVisibleFrontend($grade['visibleFrontend']);
            }

            $manager->persist($schoolGrade);
            $this->addReference($reference, $schoolGrade);
        }

        $manager->flush();
    }
}
