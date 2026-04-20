<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\School;
use App\Repository\SchoolYearRepository;
use App\Service\Educations\DefaultEducationBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddDefaultSchoolEducationsEventSubscriber implements EventSubscriberInterface
{
    private DefaultEducationBuilder $defaultEducationBuilder;
    private SchoolYearRepository $schoolYearRepository;

    public function __construct(
        SchoolYearRepository $schoolYearRepository,
        DefaultEducationBuilder $defaultEducationBuilder
    ) {
        $this->defaultEducationBuilder = $defaultEducationBuilder;
        $this->schoolYearRepository = $schoolYearRepository;
    }

    /**
     * @return array<class-string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [BeforeEntityPersistedEvent::class => 'addDefaultEducations'];
    }

    public function addDefaultEducations(BeforeEntityPersistedEvent $event): void
    {
        $school = $event->getEntityInstance();
        if (!$school instanceof School || null !== $school->getId()) {
            return;
        }

        $defaultEducations = new ArrayCollection();
        $schoolYears = $this->schoolYearRepository->findAll();
        foreach ($schoolYears as $schoolYear) {
            $schoolYearDefaultEducations = $this->defaultEducationBuilder->build($school, $schoolYear);
            foreach ($schoolYearDefaultEducations as $defaultEducation) {
                $defaultEducations->add($defaultEducation);
            }
        }

        $school->setEducations($defaultEducations);
    }
}
