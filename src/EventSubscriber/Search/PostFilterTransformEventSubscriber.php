<?php

declare(strict_types=1);

namespace App\EventSubscriber\Search;

use App\Event\PostFilterTransformEvent;
use App\Repository\SchoolGradeRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PostFilterTransformEventSubscriber implements EventSubscriberInterface
{
    private SchoolGradeRepository $schoolGradeRepository;

    public function __construct(SchoolGradeRepository $schoolGradeRepository)
    {
        $this->schoolGradeRepository = $schoolGradeRepository;
    }

    /**
     * @return array<class-string, list<string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PostFilterTransformEvent::class => ['addFilters'],
        ];
    }

    public function addFilters(PostFilterTransformEvent $event): void
    {
        $filter = $event->getFilter();

        if ($filter->hasSchoolLevels() && $filter->hasSchoolGrades()) {
            $grades = $this->schoolGradeRepository->findByLevels($filter->getSchoolLevels(), false);

            foreach ($grades as $grade) {
                $filter->addSchoolGrade($grade);
            }
        }
    }
}
