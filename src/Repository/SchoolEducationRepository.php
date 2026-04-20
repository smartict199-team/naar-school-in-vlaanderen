<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SchoolEducation;
use App\Entity\SchoolYear;
use App\Model\Search\SearchFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SchoolEducation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolEducation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolEducation[]    findAll()
 * @method SchoolEducation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolEducationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolEducation::class);
    }

    /**
     * @return SchoolEducation[]
     */
    public function findUniqueEducationsByFilter(SearchFilter $filter): array
    {
        $qb = $this->createFilteredQueryBuilder($filter);

        $qb->addGroupBy('e.name');

        return $qb->getQuery()->getResult() ?? [];
    }

    /**
     * @param $names string[]

     *
     * @return string[]
     */
    public function findAdministrativeGroupsByEducationNames($names): array
    {
        $qb = $this->createQueryBuilder('e');

        $orX = $qb->expr()->orX();
        foreach ($names as $index => $name) {
            $orX->add($qb->expr()->like('e.name', ':name' . $index));
            $qb->setParameter('name' . $index, '%' . $name . '%');
        }
        $qb->andWhere($orX);

        $qb->addGroupBy('e.administrativeGroups');

        $results = $qb->getQuery()->getResult() ?? [];

        $groups = [];

        foreach ($results as $education) {
            $group = $education->getAdministrativeGroups();
            if (null !== $group) {
                $groups[] = $group;
            }
        }

        return $groups;
    }

    public function createFilteredQueryBuilder(SearchFilter $filter): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e');
        $schoolYear = $filter->getSchoolYear();

        if ($filter->hasSchoolLevels()) {
            $qb->andWhere('e.level in (:schoolLevels)');
            $qb->setParameter('schoolLevels', $filter->getSchoolLevels());
        } elseif ($filter->getLevel() && $filter->getType()) {
            $qb->join('e.level', 'level');
            $qb->andWhere('level.type = :type');
            $qb->andWhere('level.level = :level');

            $qb->setParameter('type', $filter->getType());
            $qb->setParameter('level', $filter->getLevel());
        }

        if ($schoolYear instanceof SchoolYear) {
            $qb->andWhere('e.year = :year');
            $qb->setParameter('year', $schoolYear);
        }

        if ($filter->hasSchoolGrades()) {
            $qb->andWhere('e.grade IN (:schoolGrades)');
            $qb->setParameter('schoolGrades', $filter->getSchoolGrades());
        }

        if ($filter->hasCities()) {
            $qb->join('e.school', 'school');
            $qb->andWhere($qb->expr()->in('school.city', $filter->getCityNames()));
            $qb->andWhere($qb->expr()->in('school.postalCode', $filter->getCityPostalCodes()));
        }

        if ($filter->hasEducations()) {
            $qb->andWhere($qb->expr()->in(
                'e.administrativeGroups',
                $this->findAdministrativeGroupsByEducationNames($filter->getEducationNames())
            ));
        }

        if ($filter->hasSchoolTypes()) {
            $qb->andWhere('e.type in (:schoolTypes)');
            $qb->setParameter('schoolTypes', $filter->getSchoolTypes());
        }

        if ($filter->hasSchoolPhases()) {
            $qb->andWhere('e.phase in (:schoolPhases)');
            $qb->setParameter('schoolPhases', $filter->getSchoolPhases());
        }

        return $qb;
    }

    /**
     * @return SchoolEducation[]
     */
    public function findByFilter(SearchFilter $filter): array
    {
        return $this->createFilteredQueryBuilder($filter)->getQuery()->getResult() ?? [];
    }

    public function save(SchoolEducation $schoolEducation): void
    {
        $entityManager = $this->_em;

        $entityManager->transactional(function (EntityManagerInterface $entityManager) use ($schoolEducation) {
            $entityManager->persist($schoolEducation);
            $entityManager->flush();
        });
    }

    public function persist(SchoolEducation $schoolEducation): void
    {
        $this->_em->persist($schoolEducation);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
