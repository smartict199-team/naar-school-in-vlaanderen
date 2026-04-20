<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AuditLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AuditLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditLog::class);
    }

    public function save(AuditLog $auditLog): void
    {
        $entityManager = $this->_em;

        $entityManager->transactional(function (EntityManagerInterface $entityManager) use ($auditLog) {
            $entityManager->persist($auditLog);
            $entityManager->flush();
        });
    }
}
