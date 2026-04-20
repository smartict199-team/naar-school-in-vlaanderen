<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SchoolGrade;
use App\Entity\SchoolLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SchoolGrade|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolGrade|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolGrade[]    findAll()
 * @method SchoolGrade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolGradeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolGrade::class);
    }

    /**
     * @param SchoolLevel[] $levels
     *
     * @return SchoolGrade[]
     */
    public function findByLevels(array $levels, bool $visibleFrontend = true): array
    {
        $qb = $this->createQueryBuilder('g');

        $qb->join('g.levelGrades', 'levelGrade');
        $qb->andWhere('levelGrade.schoolLevel = :levels');
        $qb->setParameter('levels', $levels);

        $qb->andWhere('g.visibleFrontend = :visibleFrontend');
        $qb->setParameter('visibleFrontend', $visibleFrontend);

        return $qb->getQuery()->getResult();
    }

    public function save(SchoolGrade $schoolGrade): void
    {
        $entityManager = $this->_em;

        $entityManager->transactional(function (EntityManagerInterface $entityManager) use ($schoolGrade) {
            $entityManager->persist($schoolGrade);
            $entityManager->flush();
        });
    }

    public function findOneByInternalName(string $internalName): ?SchoolGrade
    {
        $qb = $this->createQueryBuilder('g');
        $qb->where('g.internalName = :internalName')
            ->setParameter('internalName', $internalName)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
