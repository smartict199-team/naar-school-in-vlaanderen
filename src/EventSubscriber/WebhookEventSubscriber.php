<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\School;
use App\Entity\SchoolEducation;
use App\Service\Webhook\WebhookService;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Security\Core\Security;

class WebhookEventSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private WebhookService $webhookService;

    public function __construct(Security $security, WebhookService $webhookService)
    {
        $this->security = $security;
        $this->webhookService = $webhookService;
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

        $this->publishWebhooks($unitOfWork);
    }

    private function publishWebhooks(UnitOfWork $unitOfWork): void
    {
        $updatedSchools = [];
        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof SchoolEducation) {
                continue;
            }

            $school = $entity->getSchool();

            if (!$school instanceof School) {
                continue;
            }

            $this->webhookService->publishUpdatedSchool($school, $entity->getYear());
        }
    }
}
