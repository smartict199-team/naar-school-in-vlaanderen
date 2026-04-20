<?php

declare(strict_types=1);

namespace App\Form\EventSubscriber;

use App\Entity\SchoolLevel;
use App\Form\School\SchoolGradeType;
use App\Form\School\SchoolTypeType;
use App\Model\Form\FilterForm;
use App\Service\Provider\FilterFormDataProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PostSetDataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class PrimaryFormEventSubscriber implements EventSubscriberInterface
{
    private FilterFormDataProvider $filterFormDataProvider;

    public function __construct(FilterFormDataProvider $filterFormDataProvider)
    {
        $this->filterFormDataProvider = $filterFormDataProvider;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SET_DATA => 'onPostSetData',
        ];
    }

    public function onPostSetData(PostSetDataEvent $event): void
    {
        $filterFormData = $event->getData();
        $filterForm = $event->getForm();

        if (!$filterFormData instanceof FilterForm) {
            return;
        }

        $type = $filterFormData->getType();

        if (SchoolLevel::TYPE_PRIMARY_EDUCATION !== $type) {
            return;
        }

        $level = $filterFormData->getLevel();

        switch ($level) {
            case SchoolLevel::LEVEL_REGULAR_EDUCATION:
            case SchoolLevel::LEVEL_RECEPTION_EDUCATION:
                $schoolLevel = $this->filterFormDataProvider->getLevel(SchoolLevel::LEVEL_REGULAR_EDUCATION, $type);
                $filterForm->getData()->setSchoolLevel($schoolLevel);
                $this->addSchoolGradeField($schoolLevel, $filterForm);
                break;
            case SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION:
                $schoolLevel = $this->filterFormDataProvider->getLevel($level, $type);
                $filterForm->getData()->setSchoolLevel($schoolLevel);
                $this->addSchoolTypeField($schoolLevel, $filterForm);
                break;
        }
    }

    private function addSchoolTypeField(?SchoolLevel $schoolLevel, FormInterface $filterForm): void
    {
        $filterForm->add('schoolType', SchoolTypeType::class, [
            'level' => $schoolLevel,
            'only_visible_frontend' => true,
            'label' => 'app.index.form.label.school_type',
            'placeholder' => 'app.index.form.label.school_type',
        ]);
    }

    private function addSchoolGradeField(?SchoolLevel $schoolLevel, FormInterface $filterForm): void
    {
        $filterForm->add('schoolGrade', SchoolGradeType::class, [
            'label' => 'app.index.form.label.school_grade_primary',
            'placeholder' => 'app.index.form.label.school_grade_primary',
            'level' => $schoolLevel,
            'showOnlyFrontend' => true,
        ]);
    }
}
