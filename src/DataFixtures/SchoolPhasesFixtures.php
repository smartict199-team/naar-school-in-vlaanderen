<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SchoolPhase;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SchoolPhasesFixtures extends Fixture
{
    /**
     * @var array<string, string>
     */
    public static array $phases = [
        'phase_1' => 'Fase 1',
        'phase_2' => 'Fase 2',
        'phase_observation' => 'Observatiejaar',
        'phase_education' => 'Opleidingsfase',
        'phase_qualification' => 'Kwalificatiefase',
        'phase_integration' => 'Facultatieve integratiefase',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::$phases as $reference => $name) {
            $phase = new SchoolPhase();
            $phase->setName($name);
            $manager->persist($phase);
            $this->addReference($reference, $phase);
        }

        $manager->flush();
    }
}
