<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SchoolGrade;
use App\Entity\SchoolLevel;
use App\Entity\SchoolLevelGrade;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SchoolLevelGradesFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var array<array<array{educationRef: string, gradeRef: string, position: int, default: boolean}>>
     */
    private static array $schoolLevelGradeGroups = [
        [
            [
                'educationRef' => 'ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_1',
                'position' => 0,
                'default' => false,
            ],
            [
                'educationRef' => 'ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_2',
                'position' => 1,
                'default' => false,
            ],
            [
                'educationRef' => 'ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_3',
                'position' => 2,
                'default' => false,
            ],
            [
                'educationRef' => 'ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_4',
                'position' => 3,
                'default' => false,
            ],
            [
                'educationRef' => 'ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_5',
                'position' => 4,
                'default' => false,
            ],
        ],
        [
            [
                'educationRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_1',
                'position' => 0,
                'default' => false,
            ],
            [
                'educationRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_2',
                'position' => 1,
                'default' => false,
            ],
            [
                'educationRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_3',
                'position' => 2,
                'default' => false,
            ],
            [
                'educationRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_4',
                'position' => 3,
                'default' => false,
            ],
            [
                'educationRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_5',
                'position' => 4,
                'default' => false,
            ],
            [
                'educationRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_6',
                'position' => 5,
                'default' => false,
            ],
            [
                'educationRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'gradeRef' => 'grade_7',
                'position' => 6,
                'default' => false,
            ],
        ],
        [
            [
                'educationRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_pre_primary_education',
                'position' => 0,
                'default' => true,
            ],
            [
                'educationRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_birth_year_2014',
                'position' => 2014,
                'default' => false,
            ],
            [
                'educationRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_birth_year_2015',
                'position' => 2015,
                'default' => false,
            ],
            [
                'educationRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_birth_year_2016',
                'position' => 2016,
                'default' => false,
            ],
            [
                'educationRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_birth_year_2017',
                'position' => 2017,
                'default' => false,
            ],
            [
                'educationRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_birth_year_2018',
                'position' => 2018,
                'default' => false,
            ],
            [
                'educationRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_birth_year_2019',
                'position' => 2019,
                'default' => false,
            ],
        ],
        [
            [
                'educationRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_1',
                'position' => 0,
                'default' => false,
            ],
            [
                'educationRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_2',
                'position' => 1,
                'default' => false,
            ],
            [
                'educationRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_3',
                'position' => 2,
                'default' => false,
            ],
            [
                'educationRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_4',
                'position' => 3,
                'default' => false,
            ],
            [
                'educationRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_5',
                'position' => 4,
                'default' => false,
            ],
            [
                'educationRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_6',
                'position' => 5,
                'default' => false,
            ],
            [
                'educationRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'gradeRef' => 'grade_primary_education',
                'position' => 6,
                'default' => true,
            ],
        ],
        [
            [
                'educationRef' => 'reception_education_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_RECEPTION_EDUCATION,
                'gradeRef' => 'grade_reception_education',
                'position' => 0,
                'default' => true,
            ],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::$schoolLevelGradeGroups as $schoolLevelGrades) {
            foreach ($schoolLevelGrades as $schoolLevelGrade) {
                $formType = new SchoolLevelGrade();

                $level = $this->getReference($schoolLevelGrade['educationRef']);
                if (!$level instanceof SchoolLevel) {
                    continue;
                }

                $grade = $this->getReference($schoolLevelGrade['gradeRef']);
                if (!$grade instanceof SchoolGrade) {
                    continue;
                }

                $formType->setSchoolLevel($level);
                $formType->setSchoolGrade($grade);
                $formType->setPosition($schoolLevelGrade['position']);
                $formType->setDefault($schoolLevelGrade['default']);

                $manager->persist($formType);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SchoolGradesFixtures::class,
            SchoolLevelFixtures::class,
        ];
    }
}
