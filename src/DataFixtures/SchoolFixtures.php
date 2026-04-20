<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\School;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\ObjectManager;

class SchoolFixtures extends Fixture
{
    private const SCHOOLS_JSON_LOCATION = './assets/fixtures/vestigingen.data.json';

    private string $source;

    public function __construct(string $source = self::SCHOOLS_JSON_LOCATION)
    {
        $this->source = $source;
    }

    public function load(ObjectManager $manager): void
    {
        $jsonLocation = $json = $this->source;

        if (false !== strpos($jsonLocation, 'https://') || file_exists($jsonLocation)) {
            $json = file_get_contents($jsonLocation);
            if (false === $json) {
                throw new \RuntimeException(sprintf('Failed retrieving data from %s', $jsonLocation));
            }
        }

        $data = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);

        foreach ($data as $key => $schoolData) {
            $school = new School();
            $school->setId($schoolData['id']);
            $school->setName($schoolData['naam']);
            $school->setAddress($schoolData['adres']);
            $school->setPostalCode($schoolData['postcode']);
            $school->setCity($schoolData['gemeente']);
            $school->setRegion($schoolData['hoofdgemeente']);
            $school->setWebsite($schoolData['website']);
            $school->setType('lager' === $schoolData['type'] ? School::TYPE_PRIMARY_EDUCATION : School::TYPE_SECONDARY_EDUCATION);

            if (\array_key_exists('vestigings_nummer', $schoolData)) {
                $establishmentNumbers = array_filter(explode(',', preg_replace('/[^0-9]/', ',', $schoolData['vestigings_nummer'])));
                $school->setEstablishmentNumbers(array_values(array_map(static fn ($establishmentNumber) => (int) $establishmentNumber, $establishmentNumbers)));
            }

            $manager->persist($school);
            $this->setReference('school_' . $school->getId(), $school);

            $schoolMetadata = $manager->getClassMetadata(School::class);
            if ($schoolMetadata instanceof ClassMetadataInfo) {
                $schoolMetadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
                $schoolMetadata->setIdGenerator(new AssignedGenerator());
            }

            if (0 === $key % 10) {
                $manager->flush();
            }
        }

        $manager->flush();
    }
}
