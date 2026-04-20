<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method School|null find($id, $lockMode = null, $lockVersion = null)
 * @method School|null findOneBy(array $criteria, array $orderBy = null)
 * @method School[]    findAll()
 * @method School[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    /**
     * @return School[]
     */
    public function findSchoolsWithDistinctCity(): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb->select('s.city')->distinct(true);

        return $qb->getQuery()->getResult() ?? [];
    }

    /**
     * @return array<School>
     */
    public function findByEstablishmentNumber(string $establishmentNumber): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb->andWhere($qb->expr()->like('s.establishmentNumbers', ':establishmentNumber'));
        $qb->setParameter('establishmentNumber', sprintf('%%%s%%', $establishmentNumber));

        return $qb->getQuery()->getResult();
    }

    public function save(School $schoolEducation): void
    {
        $entityManager = $this->_em;

        $entityManager->transactional(function (EntityManagerInterface $entityManager) use ($schoolEducation) {
            $entityManager->persist($schoolEducation);
            $entityManager->flush();
        });
    }

    public function findOneByInstitutionAndEstablishment(string $establishmentNumber, string $institutionNumber): ?School
    {
        $qb = $this->createQueryBuilder('s');

        $expr = $qb->expr()->orX(
            's.establishmentNumbers = :exact',
            's.establishmentNumbers LIKE :start',
            's.establishmentNumbers LIKE :middle',
            's.establishmentNumbers LIKE :end'
        );

        $qb->where('s.institutionNumber = :institutionNumber')
            ->andWhere($expr)
            ->setParameter('institutionNumber', $institutionNumber)
            ->setParameter('exact', $establishmentNumber)
            ->setParameter('start', $establishmentNumber . ',%')
            ->setParameter('middle', '%,' . $establishmentNumber . ',%')
            ->setParameter('end', '%,' . $establishmentNumber)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

}
