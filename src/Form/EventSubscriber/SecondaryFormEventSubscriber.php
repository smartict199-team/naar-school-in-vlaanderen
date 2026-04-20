<?php

declare(strict_types=1);

namespace App\Form\EventSubscriber;

use App\Entity\SchoolEducation;
use App\Entity\SchoolLevel;
use App\Entity\SchoolYear;
use App\Form\School\SchoolGradeType;
use App\Form\School\SchoolPhaseType;
use App\Form\School\SchoolTypeType;
use App\Model\Form\FilterForm;
use App\Service\Provider\FilterFormDataProvider;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PostSetDataEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class SecondaryFormEventSubscriber implements EventSubscriberInterface
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
        $filterForm = $event->getForm();
        $filterFormData = $event->getData();
        if (!$filterFormData instanceof FilterForm) {
            return;
        }

        $type = $filterFormData->getType();
        if (SchoolLevel::TYPE_SECONDARY_EDUCATION !== $type) {
            return;
        }

        $level = $filterFormData->getLevel();
        $schoolLevel = $filterFormData->getSchoolLevel();
        $schoolYear = $filterFormData->getSchoolYear();

        if (null !== $level && SchoolLevel::LEVEL_RECEPTION_EDUCATION !== $level) {
            $this->addSchoolLevelField($type, $level, $filterForm);
        }

        if ($schoolLevel instanceof SchoolLevel && $schoolYear instanceof SchoolYear) {
            if ($schoolLevel->isSchoolTypeFilterVisible()) {
                $this->addSchoolTypeField($schoolLevel, $filterForm);
            }

            if ($schoolLevel->isPhaseFilterVisible()) {
                $this->addPhaseField($schoolLevel, $filterForm);
            }

            if ($schoolLevel->isGradeFilterVisible()) {
                $this->addSchoolGradeField($schoolLevel, $filterForm);
            }

            if ($schoolLevel->isEducationsFilterVisible()) {
                $this->addEducationField($filterFormData, $filterForm);
            }
        }
    }

    private function addSchoolGradeField(SchoolLevel $schoolLevel, FormInterface $filterForm): void
    {
        $filterForm->add('schoolGrade', SchoolGradeType::class, [
            'label' => 'app.index.form.label.school_grade_primary',
            'placeholder' => 'app.index.form.label.school_grade_primary',
            'level' => $schoolLevel,
            'showOnlyFrontend' => true,
        ]);
    }

    private function addPhaseField(SchoolLevel $schoolLevel, FormInterface $filterForm): void
    {
        $filterForm->add('schoolPhase', SchoolPhaseType::class, [
            'level' => $schoolLevel,
            'label' => 'app.index.form.label.school_phase_type',
            'placeholder' => 'app.index.form.label.school_phase_type',
        ]);
    }

    private function addSchoolTypeField(SchoolLevel $schoolLevel, FormInterface $filterForm): void
    {
        $filterForm->add('schoolType', SchoolTypeType::class, [
            'level' => $schoolLevel,
            'label' => 'app.index.form.label.school_type',
            'placeholder' => 'app.index.form.label.school_type',
        ]);
    }

    private function addSchoolLevelField(string $schoolType, string $level, FormInterface $filterForm): void
    {
        $choices = $this->filterFormDataProvider->getSecondaryGradeChoices($schoolType, $level);

        $filterForm->add('schoolLevel', EntityType::class, [
            'label' => sprintf('app.index.form.label.school_level_secondary_%s', $level),
            'placeholder' => sprintf('app.index.form.label.school_level_secondary_%s', $level),
            'class' => SchoolLevel::class,
            'choice_label' => 'name',
            'choices' => $choices,
        ]);
    }

    private function addEducationField(FilterForm $data, FormInterface $form): void
    {
        $choices = $this->filterFormDataProvider->getEducations($data);

        $form->add('education', EntityType::class, [
            'label' => 'app.index.form.label.education',
            'placeholder' => 'app.index.form.label.education',
            'class' => SchoolEducation::class,
            'choice_label' => 'name',
            'choices' => $choices,
        ]);
    }
}
