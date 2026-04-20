<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SchoolType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SchoolTypesFixtures extends Fixture
{
    /**
     * @var array<array{typeRef: string, name:string, visibleFrontend: bool}>
     */
    public static array $types = [
        ['typeRef' => 'type_base', 'name' => 'Type basisaanbod', 'visibleFrontend' => true],
        ['typeRef' => 'type_pre_primary', 'name' => 'Kleuter', 'visibleFrontend' => false],
        ['typeRef' => 'type_primary', 'name' => 'Lager', 'visibleFrontend' => false],
        ['typeRef' => 'type_1', 'name' => 'Type 1', 'visibleFrontend' => true],
        ['typeRef' => 'type_2', 'name' => 'Type 2', 'visibleFrontend' => true],
        ['typeRef' => 'type_3', 'name' => 'Type 3', 'visibleFrontend' => true],
        ['typeRef' => 'type_4', 'name' => 'Type 4', 'visibleFrontend' => true],
        ['typeRef' => 'type_5', 'name' => 'Type 5', 'visibleFrontend' => true],
        ['typeRef' => 'type_6', 'name' => 'Type 6', 'visibleFrontend' => true],
        ['typeRef' => 'type_7', 'name' => 'Type 7', 'visibleFrontend' => true],
        ['typeRef' => 'type_8', 'name' => 'Type 8', 'visibleFrontend' => true],
        ['typeRef' => 'type_9', 'name' => 'Type 9', 'visibleFrontend' => true],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::$types as $typeSettings) {
            $type = new SchoolType();
            $type->setName($typeSettings['name']);
            $type->setVisibleFrontend($typeSettings['visibleFrontend']);
            $manager->persist($type);
            $this->addReference($typeSettings['typeRef'], $type);
        }

        $manager->flush();
    }
}
