<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\AuditLog;
use App\Entity\School;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AuditLogsFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var string[]
     */
    private static array $sources = [
        './assets/fixtures/backuplog.data.json',
        './assets/fixtures/log.data.json',
    ];

    /**
     * @var array<string, string>
     */
    private static array $fieldMap = [
        'capaciteit' => 'capacity',
        'hide' => 'hidden',
        'percentageind' => 'indicator_student_seats_percentage',
        'percentageindtonen' => 'indicator_student_seats_percentage_visible',
        'plaatsenbezet' => 'student_seats_taken',
        'plaatsenbezetind' => 'indicator_student_seats_taken',
        'volzet' => 'capacity_reached',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::$sources as $source) {
            $json = file_get_contents($source);
            if (false === $json) {
                throw new \RuntimeException(sprintf('Could not read url %s', $source));
            }

            $data = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
            $this->loadData($manager, $data);
        }
    }

    public function getDependencies(): array
    {
        return [
            SchoolEducationsFixtures::class,
            SchoolYearsFixtures::class,
        ];
    }

    /**
     * @param array{
     *  id: int,
     *  user: string,
     *  userid: string,
     *  data: string,
     *  waarde: string,
     *  school: string,
     *  schoolid?: string,
     *  datum: string,
     * }[] $data
     */
    private function loadData(ObjectManager $manager, array $data): void
    {
        foreach ($data as $log) {
            $auditLog = new AuditLog();

            if (\array_key_exists('schoolid', $log)) {
                $schoolRef = 'school_' . $log['schoolid'];
                if ($this->hasReference($schoolRef)) {
                    $school = $this->getReference($schoolRef);
                    if ($school instanceof School) {
                        $auditLog->setSchool($school);
                    }
                }
            }

            $auditLog->setName($log['school']);
            $auditLog->setNewValue($log['waarde']);
            $auditLog->setField(self::$fieldMap[$log['data']] ?? 'unknown');

            $dateTime = \DateTime::createFromFormat('Y/m/d H:i:s', $log['datum']);
            if (false !== $dateTime) {
                $auditLog->setCreatedAt($dateTime);
            }

            $auditLog->setEmail($log['user']);
            $auditLog->setUserId($log['userid']);

            $manager->persist($auditLog);
        }

        $manager->flush();
    }
}
