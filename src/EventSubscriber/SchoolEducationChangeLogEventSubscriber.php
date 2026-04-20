<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\AuditLog;
use App\Entity\SchoolEducation;
use App\Model\AuditLogFields;
use App\Model\LoggableUserInterface;
use App\Service\AuditLog\AuditLogFactory;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Security\Core\Security;
use function Symfony\Component\String\u;

class SchoolEducationChangeLogEventSubscriber implements EventSubscriberInterface
{
    private AuditLogFactory $auditLogService;
    private Security $security;

    public function __construct(AuditLogFactory $auditLogService, Security $security)
    {
        $this->auditLogService = $auditLogService;
        $this->security = $security;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush => 'onFlush',
        ];
    }

    public function onFlush(OnFlushEventArgs $event): void
    {
        $em = $event->getEntityManager();
        $unitOfWork = $em->getUnitOfWork();

        $user = $this->security->getUser();
        if (!$user instanceof LoggableUserInterface) {
            return;
        }

        foreach ($this->getSchoolEducations($unitOfWork) as $entity) {
            $changes = $unitOfWork->getEntityChangeSet($entity);
            foreach ($changes as $field => $values) {
                $fieldName = u($field)->snake()->toString();
                if (!\in_array($fieldName, AuditLogFields::$fields)) {
                    continue;
                }

                if ($entity->getYear()->getStartYear() >= 2023) {
                    $fieldName = str_replace('indicator', 'underrepresented', $fieldName);
                    $fieldName = preg_replace('/^student_seats_taken$/', 'student_seats_taken_urg', $fieldName);
                }

                $level = $entity->getLevel();
                if (null === $level) {
                    continue;
                }

                [$oldValue, $newValue] = $values;
                $log = $this->auditLogService->create(
                    $user,
                    $fieldName,
                    (string) $oldValue,
                    (string) $newValue,
                    $entity->getFullName(),
                    $entity->getSchool(),
                    $entity->getYear()
                );

                $em->persist($log);

                $resourceClass = $em->getClassMetadata(AuditLog::class);
                $unitOfWork->computeChangeSet($resourceClass, $log);
            }
        }
    }

    /**
     * @return iterable<SchoolEducation>
     */
    private function getSchoolEducations(UnitOfWork $unitOfWork): iterable
    {
        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof SchoolEducation) {
                continue;
            }

            yield $entity;
        }
    }
}
