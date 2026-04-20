<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\SchoolYear;
use App\Message\CloneSchoolEducationsMessage;
use App\Repository\SchoolEducationRepository;
use App\Repository\SchoolYearRepository;
use App\Service\Educations\EducationCloner;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CloneSchoolEducationsHandler implements MessageHandlerInterface
{
    private SchoolYearRepository $schoolYearRepository;
    private SchoolEducationRepository $schoolEducationRepository;
    private EducationCloner $educationCloner;

    public function __construct(
        SchoolYearRepository $schoolYearRepository,
        SchoolEducationRepository $schoolEducationRepository,
        EducationCloner $educationCloner
    ) {
        $this->schoolYearRepository = $schoolYearRepository;
        $this->schoolEducationRepository = $schoolEducationRepository;
        $this->educationCloner = $educationCloner;
    }

    public function __invoke(CloneSchoolEducationsMessage $message)
    {
        $oldYear = $this->schoolYearRepository->findOneBy(['id' => $message->getOldYearId()]);
        $newYear = $this->schoolYearRepository->findOneBy(['id' => $message->getNewYearId()]);

        $this->cloneSchoolYearSchoolEducations($oldYear, $newYear);
    }

    private function cloneSchoolYearSchoolEducations(SchoolYear $oldYear, SchoolYear $newYear): void
    {
        $qb = $this->schoolEducationRepository->createQueryBuilder('school_education');
        $qb->andWhere('school_education.year = :year')
            ->setParameter('year', $oldYear);

        $batchSize = 20;
        $i = 1;
        foreach ($qb->getQuery()->toIterable() as $schoolEducation) {
            $newEducation = $this->educationCloner->clone($schoolEducation, $newYear);
            $this->schoolEducationRepository->persist($newEducation);

            if (($i % $batchSize) === 0) {
                $this->schoolEducationRepository->flush();
            }

            ++$i;
        }

        $this->schoolEducationRepository->flush();
    }
}
