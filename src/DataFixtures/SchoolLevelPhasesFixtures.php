<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SchoolLevel;
use App\Entity\SchoolLevelPhase;
use App\Entity\SchoolPhase;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SchoolLevelPhasesFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var array<array<array<array-key, string>>>
     */
    private static array $schoolPhaseTypeGroups = [
        [
            ['ov2_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'phase_1'],
            ['ov2_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'phase_2'],
        ],
        [
            ['ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'phase_observation'],
            ['ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'phase_education'],
            ['ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'phase_qualification'],
            ['ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'phase_integration'],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::$schoolPhaseTypeGroups as $schoolPhaseTypeGroup) {
            foreach ($schoolPhaseTypeGroup as $key => $schoolPhase) {
                [$schoolLevelReference, $schoolPhaseReference] = $schoolPhase;

                $schoolLevelPhase = new SchoolLevelPhase();
                $schoolLevel = $this->getReference($schoolLevelReference);
                $schoolPhase = $this->getReference($schoolPhaseReference);
                if (!$schoolPhase instanceof SchoolPhase || !$schoolLevel instanceof SchoolLevel) {
                    break;
                }

                $schoolLevelPhase->setSchoolLevel($schoolLevel);
                $schoolLevelPhase->setSchoolPhase($schoolPhase);

                $schoolLevelPhase->setPosition($key);

                $manager->persist($schoolLevelPhase);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SchoolPhasesFixtures::class,
            SchoolLevelFixtures::class,
        ];
    }
}
