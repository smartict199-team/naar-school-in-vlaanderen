<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SchoolLevel;
use App\Entity\SchoolLevelType;
use App\Entity\SchoolType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SchoolLevelTypesFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var array<array<array{levelRef: string, typeRef: string, default: boolean}>>
     */
    private static array $schoolLevelTypeGroups = [
        [
            ['levelRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_pre_primary', 'default' => true],
            ['levelRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_base', 'default' => false],
            ['levelRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_2', 'default' => false],
            ['levelRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_3', 'default' => false],
            ['levelRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_4', 'default' => false],
            ['levelRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_5', 'default' => false],
            ['levelRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_6', 'default' => false],
            ['levelRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_7', 'default' => false],
            ['levelRef' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_9', 'default' => false],
        ],
        [
            ['levelRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_primary', 'default' => true],
            ['levelRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_base', 'default' => false],
            ['levelRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_1', 'default' => false],
            ['levelRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_2', 'default' => false],
            ['levelRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_3', 'default' => false],
            ['levelRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_4', 'default' => false],
            ['levelRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_5', 'default' => false],
            ['levelRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_6', 'default' => false],
            ['levelRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_7', 'default' => false],
            ['levelRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_8', 'default' => false],
            ['levelRef' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_9', 'default' => false],
        ],
        [
            ['levelRef' => 'ov1_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_2', 'default' => false],
            ['levelRef' => 'ov1_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_3', 'default' => false],
            ['levelRef' => 'ov1_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_4', 'default' => false],
            ['levelRef' => 'ov1_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_6', 'default' => false],
            ['levelRef' => 'ov1_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_7', 'default' => false],
            ['levelRef' => 'ov1_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_9', 'default' => false],
        ],
        [
            ['levelRef' => 'ov2_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_2', 'default' => false],
            ['levelRef' => 'ov2_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_3', 'default' => false],
            ['levelRef' => 'ov2_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_4', 'default' => false],
            ['levelRef' => 'ov2_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_6', 'default' => false],
            ['levelRef' => 'ov2_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_7', 'default' => false],
            ['levelRef' => 'ov2_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_9', 'default' => false],
        ],
        [
            ['levelRef' => 'ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_base', 'default' => false],
            ['levelRef' => 'ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_3', 'default' => false],
            ['levelRef' => 'ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_4', 'default' => false],
            ['levelRef' => 'ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_6', 'default' => false],
            ['levelRef' => 'ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_7', 'default' => false],
            ['levelRef' => 'ov3_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_9', 'default' => false],
        ],
        [
            ['levelRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_3', 'default' => false],
            ['levelRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_4', 'default' => false],
            ['levelRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_5', 'default' => false],
            ['levelRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_6', 'default' => false],
            ['levelRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_7', 'default' => false],
            ['levelRef' => 'ov4_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'typeRef' => 'type_9', 'default' => false],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::$schoolLevelTypeGroups as $schoolLevelTypes) {
            foreach ($schoolLevelTypes as $key => $schoolLevelType) {
                $level = $this->getReference($schoolLevelType['levelRef']);
                $type = $this->getReference($schoolLevelType['typeRef']);
                if (!$type instanceof SchoolType || !$level instanceof SchoolLevel) {
                    continue;
                }

                $formType = new SchoolLevelType();
                $formType->setSchoolLevel($level);
                $formType->setSchoolType($type);
                $formType->setDefault($schoolLevelType['default']);
                $formType->setPosition($key);

                $manager->persist($formType);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SchoolTypesFixtures::class,
            SchoolLevelFixtures::class,
        ];
    }
}
