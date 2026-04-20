<?php

declare(strict_types=1);

namespace App\Service\Search;

use App\Entity\SchoolEducation;
use App\Entity\SchoolGrade;
use App\Entity\SchoolLevel;
use App\Entity\SchoolPhase;
use App\Entity\SchoolType;
use App\Event\PostFilterTransformEvent;
use App\Model\Form\FilterForm;
use App\Model\Search\SearchFilter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FilterFormatter
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function format(FilterForm $filterForm): SearchFilter
    {
        $filter = new SearchFilter();

        $education = $filterForm->getEducation();
        $schoolGrade = $filterForm->getSchoolGrade();
        $schoolLevel = $filterForm->getSchoolLevel();
        $schoolPhase = $filterForm->getSchoolPhase();
        $schoolType = $filterForm->getSchoolType();

        $filter->setCities($filterForm->getCities());
        $filter->setLevel($filterForm->getLevel());
        $filter->setType($filterForm->getType());
        $filter->setSchoolYear($filterForm->getSchoolYear());

        if ($education instanceof SchoolEducation) {
            $filter->addEducation($education);
        }

        if ($schoolGrade instanceof SchoolGrade) {
            $filter->addSchoolGrade($schoolGrade);
        }

        if ($schoolLevel instanceof SchoolLevel) {
            $filter->addSchoolLevel($schoolLevel);
        }

        if ($schoolPhase instanceof SchoolPhase) {
            $filter->addSchoolPhase($schoolPhase);
        }

        if ($schoolType instanceof SchoolType) {
            $filter->addSchoolType($schoolType);
        }

        $this->eventDispatcher->dispatch(new PostFilterTransformEvent($filter));

        return $filter;
    }
}
