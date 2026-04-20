<?php

declare(strict_types=1);

namespace App\EventSubscriber\Log;

use App\Entity\AuditLog;
use App\Entity\SchoolEducationUnderrepresentedGroup;
use App\Model\AuditLogFields;
use App\Model\LoggableUserInterface;
use App\Service\AuditLog\AuditLogFactory;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Security\Core\Security;
use function Symfony\Component\String\u;

class UnderrepresentedGroupChangeLogEventSubscriber implements EventSubscriberInterface
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

        foreach ($this->getUnderrepresentedGroups($unitOfWork) as $entity) {
            $changes = $unitOfWork->getEntityChangeSet($entity);
            foreach ($changes as $field => $values) {
                $fieldName = u($field)->snake()->toString();

                if (!\in_array($fieldName, AuditLogFields::$underrepresentedGroupFields, true)) {
                    continue;
                }

                $education = $entity->getSchoolEducation();
                if (null === $education) {
                    continue;
                }
                $level = $education->getLevel();
                if (null === $level) {
                    continue;
                }

                [$oldValue, $newValue] = $values;
                $log = $this->auditLogService->create(
                    $user,
                    sprintf('%s%s', AuditLogFields::LOG_PREFIX, $fieldName),
                    (string) $oldValue,
                    (string) $newValue,
                    $education->getFullName(),
                    $education->getSchool(),
                    $education->getYear()
                );

                $em->persist($log);

                $resourceClass = $em->getClassMetadata(AuditLog::class);
                $unitOfWork->computeChangeSet($resourceClass, $log);
            }
        }
    }

    /**
     * @return iterable<SchoolEducationUnderrepresentedGroup>
     */
    private function getUnderrepresentedGroups(UnitOfWork $unitOfWork): iterable
    {
        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof SchoolEducationUnderrepresentedGroup) {
                continue;
            }

            yield $entity;
        }
    }
}
