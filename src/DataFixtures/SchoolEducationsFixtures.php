<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\School;
use App\Entity\SchoolEducation;
use App\Entity\SchoolFormType;
use App\Entity\SchoolGrade;
use App\Entity\SchoolLevel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;

class SchoolEducationsFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var array<string, string>
     */
    private static array $primaryEducationMap = [
        'Kleuteronderwijs' => 'kleuter_' . SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
        'Lager Onderwijs' => 'lager_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
        'Onthaalonderwijs anderstaligen' => 'reception_education_' . SchoolLevel::TYPE_PRIMARY_EDUCATION . '_' . SchoolLevel::LEVEL_RECEPTION_EDUCATION,
    ];

    /**
     * @var array<string, array<string, string|null>>
     */
    private static array $secondaryEducationMap = [
        'Eerste graad' => [
            '1ste leerjaar' => '1ste_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
            '2de leerjaar' => '2de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
        ],
        'Tweede graad' => [
            '1ste leerjaar' => '3de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
            '2de leerjaar' => '4de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
        ],
        'Derde graad' => [
            '1ste leerjaar' => '5de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
            '2de leerjaar' => '6de_middelbaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
        ],
        '7e jaren' => [
            '' => '7e_jaar_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
        ],
        'Se-n-Se' => [
            '' => 'se_n_se_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_REGULAR_EDUCATION,
        ],
        'Onthaalonderwijs' => [
            '' => 'onthaalonderwijs_' . SchoolLevel::TYPE_SECONDARY_EDUCATION . '_' . SchoolLevel::LEVEL_RECEPTION_EDUCATION,
        ],
        '' => [
            '' => null,
        ],
    ];

    /**
     * @var array<string, string>
     */
    private static array $gradeMap = [
        '1ste leerjaar' => 'grade_1',
        '2de leerjaar' => 'grade_2',
        '3de leerjaar' => 'grade_3',
        '4de leerjaar' => 'grade_4',
        '5de leerjaar' => 'grade_5',
        '6de leerjaar' => 'grade_6',
        'Geboortejaar 2014' => 'grade_birth_year_2014',
        'Geboortejaar 2015' => 'grade_birth_year_2015',
        'Geboortejaar 2016' => 'grade_birth_year_2016',
        'Geboortejaar 2017' => 'grade_birth_year_2017',
        'Geboortejaar 2018' => 'grade_birth_year_2018',
        'Geboortejaar 2019' => 'grade_birth_year_2019',
        'Onthaalonderwijs anderstaligen' => 'grade_reception_education',
        'Kleuteronderwijs' => 'grade_pre_primary_education',
        'Lager Onderwijs' => 'grade_primary_education',
    ];

    /**
     * @var array<string, string>
     */
    private static array $formTypeMapping = [
        'Algemeen secundair onderwijs' => 'form_type_aso',
        'Algemeen Secundair Onderwijs' => 'form_type_aso',
        'ASO' => 'form_type_aso',
        'ASO ' => 'form_type_aso',
        'Andere' => 'form_type_other',
        'Beroeps secundair onderwijs' => 'form_type_bso',
        'Beroepssecundair onderwijs' => 'form_type_bso',
        'Beroepssecundair Onderwijs' => 'form_type_bso',
        'Beroeps Secundair Onderwijs' => 'form_type_bso',
        'BSO' => 'form_type_bso',
        'bso' => 'form_type_bso',
        'Gemeenschappelijk secundair onderwijs' => 'form_type_gso',
        'GSO' => 'form_type_gso',
        'gemeenschappelijk secundair onderwijs' => 'form_type_gso',
        'Gemeenschappelijk secundair' => 'form_type_gso',
        'Hoger beroepsonderwijs' => 'form_type_hbo',
        'KSO' => 'form_type_kso',
        'Kunst secundair onderwijs' => 'form_type_kso',
        'Kunstsecundair onderwijs' => 'form_type_kso',
        'Technisch secundair onderwijs' => 'form_type_tso',
        'Technische secundair onderwijs' => 'form_type_tso',
        'Technisch Secundair Onderwijs' => 'form_type_tso',
        'TSO' => 'form_type_tso',
        'OKAN' => 'form_type_gso',
        'Duaal onderwijs' => 'form_type_duaal',
        'Duaal' => 'form_type_duaal',
        'Duaal BSO' => 'form_type_duaal',
        'B-stroom' => 'form_type_gso',
        'B stroom' => 'form_type_gso',
        'B-stroom (E&O-K&C)' => 'form_type_gso',
        'B-stroom (E&O-M&W)' => 'form_type_gso',
        'B-stroom (E&O)' => 'form_type_gso',
        'B-stroom (K&C-M&W)' => 'form_type_gso',
        'B-stroom (M&W)' => 'form_type_gso',
        '2BVL' => 'form_type_gso',
        '2A' => 'form_type_gso',
        '1B' => 'form_type_gso',
        '1A' => 'form_type_gso',
        'A-stroom' => 'form_type_gso',
    ];

    /**
     * @var array<string, string>
     */
    private static array $sources = [
        'primary' => './assets/fixtures/opleidingenlager.data.json',
        'secondary' => './assets/fixtures/opleidingensecundair.data.json',
    ];

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $fixtureLogger)
    {
        $this->logger = $fixtureLogger;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::$sources as $type => $source) {
            $json = file_get_contents($source);
            if (false === $json) {
                throw new \RuntimeException(sprintf('Could not read url %s', $source));
            }

            $data = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
            $this->loadData($manager, $data, $type);
        }
    }

    public function getDependencies(): array
    {
        return [SchoolFixtures::class];
    }

    /**
     * @param array{id: int, vestiging_id: int, opleiding: string, leerjaar: string} $educationData
     */
    private function getPrimaryEducation(array $educationData, School $school): ?SchoolEducation
    {
        $education = new SchoolEducation();
        $education->setSchool($school);

        $grade = $this->getGrade($educationData);
        if (null === $grade) {
            return null;
        }

        $education->setGrade($grade);

        $level = $this->getLevel($educationData);
        if (null === $level) {
            return null;
        }

        $education->setLevel($level);

        if ($grade === $this->getReference('grade_reception_education')) {
            $education->setDefault(true);
        }

        return $education;
    }

    /**
     * @param array{opleiding: string} $educationData
     */
    private function getLevel(array $educationData): ?SchoolLevel
    {
        $levelRef = self::$primaryEducationMap[$educationData['opleiding']] ?? null;

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
     * @param array{opleiding: string, leerjaar: string} $educationData
     */
    private function getGrade(array $educationData): ?SchoolGrade
    {
        $gradeReference = self::$gradeMap[$educationData['leerjaar']] ?? self::$gradeMap[$educationData['opleiding']] ?? null;

        if (null !== $gradeReference && $this->hasReference($gradeReference)) {
            $grade = $this->getReference($gradeReference);
            if ($grade instanceof SchoolGrade) {
                return $grade;
            }
        }

        $this->logger->info(sprintf('Unable to determine grade %s', json_encode($educationData)));

        return null;
    }

    private function getSchool(int $schoolId): ?School
    {
        $schoolRef = 'school_' . $schoolId;
        if (!$this->hasReference($schoolRef)) {
            $this->logger->info(sprintf('Could not find school id %s', $schoolId));

            return null;
        }

        $school = $this->getReference($schoolRef);
        if (!$school instanceof School) {
            return null;
        }

        return $school;
    }

    /**
     * @param array<array-key, array{id: int, vestiging_id: int, graad: string, opleiding: string, leerjaar: string, administratieve_code: string, vormtype: string|null}> $data
     */
    private function loadData(ObjectManager $manager, array $data, string $type): void
    {
        foreach ($data as $key => $educationData) {
            $school = $this->getSchool($educationData['vestiging_id']);
            if (null === $school) {
                continue;
            }

            foreach (SchoolYearsFixtures::$schoolYears as $year => $yearSettings) {
                $yearRef = $this->getReference('year_' . $year);
                if ('primary' === $type) {
                    $education = $this->getPrimaryEducation($educationData, $school);
                } else {
                    $education = $this->getSecondaryEducation($educationData, $school);
                }

                if (null !== $education) {
                    $education->setYear($yearRef);
                    $this->setReference(sprintf('education_%s_%s_%s', $type, $educationData['id'], $year), $education);

                    $manager->persist($education);
                }
            }

            if (0 === (int) $key % 30) {
                $manager->flush();
            }
        }

        $manager->flush();
    }

    /**
     * @param array{graad: string, vestiging_id: int, opleiding: string, leerjaar: string, administratieve_code: string, vormtype: string|null} $educationData
     */
    private function getSecondaryEducation(array $educationData, School $school): ?SchoolEducation
    {
        $education = new SchoolEducation();
        $education->setName($educationData['opleiding']);
        $education->setSchool($school);

        $levelRef = self::$secondaryEducationMap[$educationData['graad']][$educationData['leerjaar']] ?? null;

        if (null === $levelRef || !$this->hasReference($levelRef)) {
            $this->logger->info(sprintf('Unmatched level %s', json_encode($educationData)));

            return null;
        }

        $level = $this->getReference($levelRef);
        if (!$level instanceof SchoolLevel) {
            $this->logger->info(sprintf('Ref %s expected class SchoolLevel, %s returned', $levelRef, \get_class($level)));

            return null;
        }

        $education->setLevel($level);
        $education->setAdministrativeGroups($educationData['administratieve_code']);

        if (null !== $educationData['vormtype']) {
            $education->setFormType($this->getFormType($educationData['vormtype']));
        }

        return $education;
    }

    private function getFormType(string $formName): ?SchoolFormType
    {
        $formTypeRef = self::$formTypeMapping[$formName] ?? null;

        if (null === $formTypeRef || !$this->hasReference($formTypeRef)) {
            $this->logger->info(sprintf('Unmatched form type "%s"', json_encode($formName)));

            return null;
        }

        $formType = $this->getReference($formTypeRef);
        if ($formType instanceof SchoolFormType) {
            return $formType;
        }

        $this->logger->info(sprintf('Unmatched form type (ref) "%s"', json_encode($formName)));

        return null;
    }
}
