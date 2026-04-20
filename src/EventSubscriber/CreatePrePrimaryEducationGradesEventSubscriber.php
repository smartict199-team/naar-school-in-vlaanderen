<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\SchoolGrade;
use App\Entity\SchoolLevel;
use App\Entity\SchoolLevelGrade;
use App\Entity\SchoolYear;
use App\Repository\SchoolGradeRepository;
use App\Repository\SchoolLevelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreatePrePrimaryEducationGradesEventSubscriber implements EventSubscriberInterface
{
    private SchoolGradeRepository $schoolGradeRepository;
    private SchoolLevelRepository $schoolLevelRepository;

    public function __construct(SchoolGradeRepository $schoolGradeRepository, SchoolLevelRepository $schoolLevelRepository)
    {
        $this->schoolGradeRepository = $schoolGradeRepository;
        $this->schoolLevelRepository = $schoolLevelRepository;
    }

    /**
     * @return array<class-string, array{string, int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [BeforeEntityPersistedEvent::class => ['addGrades', 10]];
    }

    public function addGrades(BeforeEntityPersistedEvent $event): void
    {
        $year = $event->getEntityInstance();
        if (!$year instanceof SchoolYear || null !== $year->getId()) {
            return;
        }

        $range = range($year->getStartYear() - 5, $year->getStartYear() - 2);
        $schoolLevel = $this->schoolLevelRepository->findOneBy(['internalName' => SchoolLevel::INTERNAL_NAME_PRE_PRIMARY_REGULAR_EDUCATION]);

        foreach ($range as $position => $birthYear) {
            $internalName = SchoolGrade::BIRTH_YEAR_INTERNAL_NAME_PREFIX . $birthYear;
            $grade = $this->schoolGradeRepository->findOneBy(['internalName' => $internalName]);
            if (null === $grade) {
                $grade = $this->createNewGrade($birthYear, $internalName);

                if (null !== $schoolLevel) {
                    $levelGrade = new SchoolLevelGrade();
                    $levelGrade->setSchoolGrade($grade);
                    $levelGrade->setSchoolLevel($schoolLevel);
                    $levelGrade->setPosition($birthYear);
                    $grade->setLevelGrades(new ArrayCollection([$levelGrade]));
                }
            }

            $grade->getYears()->add($year);
            $this->schoolGradeRepository->save($grade);
        }

        if (null !== $schoolLevel) {
            $defaultGrade = $schoolLevel->getDefaultGrade();
            if ($defaultGrade instanceof SchoolGrade) {
                $defaultGrade->getYears()->add($year);
                $this->schoolGradeRepository->save($defaultGrade);
            }
        }
    }

    private function createNewGrade(int $birthYear, string $internalName): SchoolGrade
    {
        $grade = new SchoolGrade();
        $grade->setName('Geboortejaar ' . $birthYear);
        $grade->setInternalName($internalName);

        return $grade;
    }
}
