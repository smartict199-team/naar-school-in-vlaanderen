<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SchoolYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SchoolYear|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolYear|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolYear[]    findAll()
 * @method SchoolYear[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolYearRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolYear::class);
    }

    public function findLastSchoolYear(): ?SchoolYear
    {
        $qb = $this->createQueryBuilder('y');

        $qb->orderBy('y.endYear', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return SchoolYear[]
     */
    public function findActiveFrontendSchoolYears(): array
    {
        $qb = $this->createQueryBuilder('y');

        $qb->andWhere('y.visibleFrontend = true');
        $qb->orderBy('y.endYear', 'ASC');

        return $qb->getQuery()->getResult() ?? [];
    }

    /**
     * @return SchoolYear[]
     */
    public function findActiveBackendSchoolYears(): array
    {
        $qb = $this->createQueryBuilder('y');
        $qb->andWhere('y.visibleBackend = true')
            ->orderBy('y.startYear', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function save(SchoolYear $schoolYear): void
    {
        $entityManager = $this->_em;

        $entityManager->transactional(function (EntityManagerInterface $entityManager) use ($schoolYear) {
            $entityManager->persist($schoolYear);
            $entityManager->flush();
        });
    }

    public function findYearByStartAndEndYear(int $startYear, int $endYear): ?SchoolYear
    {
        $qb = $this->createQueryBuilder('y');

        $qb->andWhere('y.startYear = :startYear')
            ->setParameter('startYear', $startYear)
            ->andWhere('y.endYear = :endYear')
            ->setParameter('endYear', $endYear);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findPreviousYear(int $currentStartYear, int $currentEndYear): ?SchoolYear
    {
        $qb = $this->createQueryBuilder('y');

        $qb->andWhere('y.startYear < :startYear')
            ->setParameter('startYear', $currentStartYear)
            ->andWhere('y.endYear < :endYear')
            ->setParameter('endYear', $currentEndYear);

        $qb->orderBy('y.endYear', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
