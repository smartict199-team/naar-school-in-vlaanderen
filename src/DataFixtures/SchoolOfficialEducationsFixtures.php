<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\SchoolFormType;
use App\Entity\SchoolLevel;
use App\Entity\SchoolOfficialEducation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;

class SchoolOfficialEducationsFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var string[]
     */
    private static array $textToStrip = [
        'Sense',
        'SenSe',
        '1e lj',
        '2e lj',
        '3e lj',
        '1elj',
        '2elj',
        '3elj',
        '1e j.',
        '2e j.',
        '3e j.',
        '1e jaar',
        '2e jaar',
        '3e jaar',
        '1egr1elj',
        '1egr2elj',
        '2egr1elj',
        '2egr2elj',
        '3egr1elj',
        '3egr2elj',
        '1e gr',
        '2e gr',
        '3e gr',
        '1egr',
        '2egr',
        '3egr',
        'BSO',
        'ASO',
        'TSO',
        'KSO',
    ];

    /**
     * @var array<string, array<string, string>>
     */
    private static array $secondaryEducationMap = [
        '1' => [
            '1' => '1ste_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
            '2' => '2de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
        ],
        '2' => [
            '1' => '3de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
            '2' => '4de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
        ],
        '3' => [
            '1' => '5de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
            '2' => '6de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
            '3' => '7e_jaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
        ],
        'Se-n-Se' => [
            'Se-n-Se' => 'se_n_se_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
        ],
        '9' => [
            '1' => 'onthaalonderwijs_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_RECEPTION_EDUCATION,
        ],
    ];

    /**
     * @var array<string, string>
     */
    private static array $formTypeMapping = [
        'Algemeen secundair onderwijs' => 'form_type_aso',
        'Gemeenschappelijk secundair onderwijs' => 'form_type_gso',
        'Beroepssecundair onderwijs' => 'form_type_bso',
        'Hoger beroepsonderwijs' => 'form_type_hbo',
        'Kunstsecundair onderwijs' => 'form_type_kso',
        'Technisch secundair onderwijs' => 'form_type_tso',
    ];

    private static string $source = './assets/fixtures/onderwijskiezeralle.data.json';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $fixtureLogger)
    {
        $this->logger = $fixtureLogger;
    }

    public function load(ObjectManager $manager): void
    {
        $json = file_get_contents(self::$source);
        if (false === $json) {
            throw new \RuntimeException(sprintf('Could not read url %s', self::$source));
        }

        $data = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
        $this->loadData($manager, $data);
    }

    public function getDependencies(): array
    {
        return [SchoolFixtures::class];
    }

    /**
     * @param array{leerjaar: string, graad_eht: string, korte_naam: string} $educationData
     */
    private function getLevel(array $educationData): ?SchoolLevel
    {
        if (false !== strpos($educationData['korte_naam'], 'SenSe')) {
            $levelRef = self::$secondaryEducationMap['Se-n-Se']['Se-n-Se'] ?? null;
        } else {
            $levelRef = self::$secondaryEducationMap[$educationData['graad_eht']][$educationData['leerjaar']] ?? null;
        }

        if (null !== $levelRef && $this->hasReference($levelRef)) {
            $level = $this->getReference($levelRef);
            if ($level instanceof SchoolLevel) {
                return $level;
            }
        }

        $this->logger->info(sprintf('Unable to determine level %s', json_encode($educationData)));

        return null;
    }

    /**
     * @param array<array-key, array{
     *  "id": int,
     *  "nummer_instelling": string,
     *  "korte_naam_instell": string,
     *  "korte_naam": string,
     *  "ko_onderwijsvorm_s": string,
     *  "gemeente": string,
     *  "natuurlijk_leerjr": int,
     *  "leerjaar": string,
     *  "graad_eht": string,
     * }> $data
     */
    private function loadData(ObjectManager $manager, array $data): void
    {
        foreach ($data as $key => $educationData) {
            $education = $this->getEducation($educationData);
            if (null !== $education) {
                $manager->persist($education);
            }

            if (0 === (int) $key % 30) {
                $manager->flush();
            }
        }

        $manager->flush();
    }

    /**
     * @param array{
     *  "id": int,
     *  "nummer_instelling": string,
     *  "korte_naam_instell": string,
     *  "korte_naam": string,
     *  "ko_onderwijsvorm_s": string,
     *  "gemeente": string,
     *  "natuurlijk_leerjr": int,
     *  "leerjaar": string,
     *  "graad_eht": string,
     * } $educationData
     */
    private function getEducation(array $educationData): ?SchoolOfficialEducation
    {
        $formTypeName = $educationData['ko_onderwijsvorm_s'] ?? null;
        $establishmentNumber = $educationData['nummer_instelling'] ?? null;
        if (!$formTypeName || !$establishmentNumber) {
            return null;
        }

        $formType = $this->getFormType($educationData);
        if (null === $formType) {
            return null;
        }

        $education = new SchoolOfficialEducation();
        $education->setFormType($formType);
        $education->setName($this->cleanName($educationData['korte_naam']));
        $education->setEstablishmentNumber((int) $establishmentNumber);

        $level = $this->getLevel($educationData);
        if (!$level instanceof SchoolLevel) {
            return null;
        }

        $education->setLevel($level);

        return $education;
    }

    /**
     * @param array{ko_onderwijsvorm_s: string, korte_naam: string} $educationData
     */
    private function getFormType(array $educationData): ?SchoolFormType
    {
        if (false !== strpos($educationData['korte_naam'], 'duaal')) {
            $formTypeRef = 'form_type_duaal';
        } else {
            $formTypeRef = self::$formTypeMapping[$educationData['ko_onderwijsvorm_s']] ?? null;
        }

        if (null === $formTypeRef || !$this->hasReference($formTypeRef)) {
            $this->logger->info(sprintf('Unmatched form type "%s"', json_encode($educationData)));

            return null;
        }

        $formType = $this->getReference($formTypeRef);
        if ($formType instanceof SchoolFormType) {
            return $formType;
        }

        $this->logger->info(sprintf('Unmatched form type (ref) "%s"', json_encode($educationData)));

        return null;
    }

    private function cleanName(string $name): string
    {
        $nameWithWordsStripped = str_replace(self::$textToStrip, '', $name);

        return trim(preg_replace('!\s+!', ' ', $nameWithWordsStripped));
    }
}
