<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SchoolFormType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SchoolFormTypesFixtures extends Fixture
{
    /**
     * @var array<string, string>
     */
    public static array $formTypes = [
        'form_type_aso' => 'ASO',
        'form_type_bso' => 'BSO',
        'form_type_gso' => 'GSO',
        'form_type_hbo' => 'HBO',
        'form_type_kso' => 'KSO',
        'form_type_tso' => 'TSO',
        'form_type_duaal' => 'Duaal Leren',
        'form_type_other' => 'Andere',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::$formTypes as $reference => $name) {
            $formType = new SchoolFormType();
            $formType->setName($name);
            $manager->persist($formType);
            $this->addReference($reference, $formType);
        }

        $manager->flush();
    }
}
