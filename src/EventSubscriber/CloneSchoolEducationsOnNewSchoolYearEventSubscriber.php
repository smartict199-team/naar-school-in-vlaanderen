<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\SchoolYear;
use App\Message\CloneSchoolEducationsMessage;
use App\Repository\SchoolEducationRepository;
use App\Repository\SchoolYearRepository;
use App\Service\Educations\EducationCloner;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CloneSchoolEducationsOnNewSchoolYearEventSubscriber implements EventSubscriberInterface
{
    private SchoolEducationRepository $schoolEducationRepository;
    private SchoolYearRepository $schoolYearRepository;
    private EducationCloner $educationCloner;
    private MessageBusInterface $messageBus;

    public function __construct(
        SchoolEducationRepository $schoolEducationRepository,
        SchoolYearRepository $schoolYearRepository,
        EducationCloner $educationCloner,
        MessageBusInterface $messageBus
    ) {
        $this->schoolEducationRepository = $schoolEducationRepository;
        $this->schoolYearRepository = $schoolYearRepository;
        $this->educationCloner = $educationCloner;
        $this->messageBus = $messageBus;
    }

    /**
     * @return array<class-string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [BeforeEntityPersistedEvent::class => 'onEntityPersisted'];
    }

    public function onEntityPersisted(BeforeEntityPersistedEvent $event): void
    {
        $newSchoolYear = $event->getEntityInstance();
        if (!$newSchoolYear instanceof SchoolYear) {
            return;
        }

        $previousSchoolYear = $this->getPreviousSchoolYear($newSchoolYear);
        if (!$previousSchoolYear instanceof SchoolYear) {
            return;
        }

        $this->messageBus->dispatch(new CloneSchoolEducationsMessage(
            $previousSchoolYear->getId(),
            $newSchoolYear->getId()
        ));
    }

    private function getPreviousSchoolYear(SchoolYear $year): ?SchoolYear
    {
        return $this->schoolYearRepository->findPreviousYear($year->getStartYear(), $year->getEndYear());
    }
}
