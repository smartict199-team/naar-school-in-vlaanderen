<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SchoolLevel;
use App\Entity\SchoolLevelFormType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SchoolLevelFormTypesFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var array<array<array<array-key, string>>>
     */
    private static array $schoolLevelFormTypeGroups = [
        [
            ['1ste_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_aso'],
            ['1ste_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_bso'],
            ['1ste_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_gso'],
            ['1ste_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_hbo'],
            ['1ste_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_kso'],
            ['1ste_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_tso'],
            ['1ste_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_duaal'],
        ],
        [
            ['2de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_aso'],
            ['2de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_bso'],
            ['2de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_gso'],
            ['2de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_hbo'],
            ['2de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_kso'],
            ['2de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_tso'],
            ['2de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_duaal'],
        ],
        [
            ['3de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_aso'],
            ['3de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_bso'],
            ['3de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_gso'],
            ['3de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_hbo'],
            ['3de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_kso'],
            ['3de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_tso'],
            ['3de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_duaal'],
        ],
        [
            ['4de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_aso'],
            ['4de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_bso'],
            ['4de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_gso'],
            ['4de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_hbo'],
            ['4de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_kso'],
            ['4de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_tso'],
            ['4de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_duaal'],
        ],
        [
            ['5de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_aso'],
            ['5de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_bso'],
            ['5de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_gso'],
            ['5de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_hbo'],
            ['5de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_kso'],
            ['5de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_tso'],
            ['5de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_duaal'],
        ],
        [
            ['6de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_aso'],
            ['6de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_bso'],
            ['6de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_gso'],
            ['6de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_hbo'],
            ['6de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_kso'],
            ['6de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_tso'],
            ['6de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_duaal'],
        ],
        [
            ['se_n_se_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_aso'],
            ['se_n_se_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_bso'],
            ['se_n_se_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_gso'],
            ['se_n_se_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_hbo'],
            ['se_n_se_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_kso'],
            ['se_n_se_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_tso'],
            ['se_n_se_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_duaal'],
        ],
        [
            ['7e_jaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_aso'],
            ['7e_jaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_bso'],
            ['7e_jaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_gso'],
            ['7e_jaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_hbo'],
            ['7e_jaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_kso'],
            ['7e_jaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_tso'],
            ['7e_jaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION, 'form_type_duaal'],
        ],
        [
            ['onthaalonderwijs_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_RECEPTION_EDUCATION, 'form_type_aso'],
            ['onthaalonderwijs_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_RECEPTION_EDUCATION, 'form_type_bso'],
            ['onthaalonderwijs_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_RECEPTION_EDUCATION, 'form_type_gso'],
            ['onthaalonderwijs_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_RECEPTION_EDUCATION, 'form_type_hbo'],
            ['onthaalonderwijs_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_RECEPTION_EDUCATION, 'form_type_kso'],
            ['onthaalonderwijs_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_RECEPTION_EDUCATION, 'form_type_tso'],
            ['onthaalonderwijs_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_RECEPTION_EDUCATION, 'form_type_duaal'],
        ],
        [
            ['kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_aso'],
            ['kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_bso'],
            ['kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_gso'],
            ['kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_hbo'],
            ['kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_kso'],
            ['kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_tso'],
            ['kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_duaal'],
        ],
        [
            ['lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_aso'],
            ['lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_bso'],
            ['lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_gso'],
            ['lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_hbo'],
            ['lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_kso'],
            ['lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_tso'],
            ['lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION, 'form_type_duaal'],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::$schoolLevelFormTypeGroups as $schoolLevelFormTypes) {
            foreach ($schoolLevelFormTypes as $key => $schoolLevelFormType) {
                [$schoolLevelReference, $schoolFormTypeReference] = $schoolLevelFormType;

                $formType = new SchoolLevelFormType();
                $formType->setSchoolLevel($this->getReference($schoolLevelReference));
                $formType->setSchoolFormType($this->getReference($schoolFormTypeReference));
                $formType->setPosition($key);

                $manager->persist($formType);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SchoolFormTypesFixtures::class,
            SchoolLevelFixtures::class,
        ];
    }
}
