<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DisplaySchoolSuccessMessagePostUpdateEventSubscriber implements EventSubscriberInterface
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return array<class-string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityUpdatedEvent::class => 'addUpdatedFlash',
            AfterEntityPersistedEvent::class => 'addCreatedFlash',
        ];
    }

    public function addUpdatedFlash(AfterEntityUpdatedEvent $event): void
    {
        $session = $this->session;
        if (!$session instanceof Session) {
            return;
        }

        $session->getFlashBag()->add('success', 'app.admin.schools.edit.success');
    }

    public function addCreatedFlash(AfterEntityPersistedEvent $event): void
    {
        $session = $this->session;
        if (!$session instanceof Session) {
            return;
        }

        $session->getFlashBag()->add('success', 'app.admin.schools.create.success');
    }
}
