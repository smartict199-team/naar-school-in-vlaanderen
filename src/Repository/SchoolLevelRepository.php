<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SchoolLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SchoolLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolLevel[]    findAll()
 * @method SchoolLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolLevel::class);
    }

    /**
     * @param array<array-key, string> $levels
     * @param array<array-key, string> $types
     *
     * @return SchoolLevel[]
     */
    public function findByLevelsTypes(array $levels, array $types): array
    {
        $qb = $this->createQueryBuilder('school_level');
        $qb->andWhere('school_level.type IN (:types)')
            ->setParameter('types', $types)
            ->andWhere('school_level.level IN (:levels)')
            ->setParameter('levels', $levels);

        $qb->orderBy('school_level.position', 'ASC');

        $schoolLevels = $qb->getQuery()->getResult();

        return \array_filter($schoolLevels, function (SchoolLevel $level) use ($types, $levels) {
            if ($level->isPrePrimaryEducation() && $level->isReceptionEducation() && in_array(SchoolLevel::TYPE_PRIMARY_EDUCATION, $types)) {
                return false;
            }

            return true;
        });
    }
}
