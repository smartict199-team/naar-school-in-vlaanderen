<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SchoolLevel;
use App\Entity\SchoolOfficialEducation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SchoolOfficialEducation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolOfficialEducation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolOfficialEducation[]    findAll()
 * @method SchoolOfficialEducation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolOfficialEducationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolOfficialEducation::class);
    }

    /**
     * @param SchoolLevel[] $levels
     * @param int[] $establishmentNumbers
     *
     * @return SchoolOfficialEducation[]
     */
    public function findByLevelsEstablishmentNumbers(array $levels, array $establishmentNumbers): array
    {
        $qb = $this->createQueryBuilder('school_official_education');
        $qb->andWhere('school_official_education.level in (:levels)')
            ->setParameter('levels', $levels)
            ->andWhere('school_official_education.establishmentNumber in (:establishmentNumbers)')
            ->setParameter('establishmentNumbers', $establishmentNumbers);

        return $qb->getQuery()->getResult();
    }
}
