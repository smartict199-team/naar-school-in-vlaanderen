<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Entity\SchoolEducation;
use App\Entity\SchoolLevel;
use App\Entity\SchoolYear;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolNumbersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $level = $options['level'];
        $year = $options['year'];

        if (!$level instanceof SchoolLevel || !$year instanceof SchoolYear) {
            throw new \RuntimeException('Not all required options present');
        }

        if ($level->isCapacityRequired()) {
            $builder->add('capacity', NumberType::class, [
                'label' => 'app.admin.schools.general.capacity',
                'required' => false,
            ]);
        }

        if ($year->getStartYear() >= 2023 && !$level->isReceptionEducation()) {
            $builder->add('underrepresentedGroups', SchoolEducationUnderrepresentedGroupsType::class, [
                'label' => false,
                'allow_add' => true,
            ]);
        }

        if ($level->isIndicatorStudentSeatsPercentageRequired()) {
            $builder->add('indicatorStudentSeatsPercentage', NumberType::class, [
                'label' => 'app.admin.schools.general.indicator_student_seats_percentage',
                'required' => false,
            ]);

            $builder->add('indicatorStudentSeatsPercentageVisible', CheckboxType::class, [
                'label' => 'app.admin.schools.general.indicator_student_seats_percentage_visible',
                'required' => false,
                'label_attr' => [
                    'class' => 'switch-custom',
                ],
            ]);
        }

        if ($level->isIndicatorStudentSeatsRequired()) {
            $builder->add('indicatorStudentSeatsTaken', NumberType::class, [
                'label' => 'app.admin.schools.general.indicator_student_seats_taken',
                'required' => false,
            ]);
        }

        if ($level->isStudentSeatsRequired()) {
            $builder->add('studentSeatsTaken', NumberType::class, [
                'label' => 'app.admin.schools.general.student_seats_taken',
                'required' => false,
            ]);
        }

        if ($level->isCapacityReachedRequired()) {
            $builder->add('capacityReached', CheckboxType::class, [
                'label' => false,
                'required' => false,
                'label_attr' => [
                    'class' => 'switch-custom',
                ],
            ]);
        }

        if ($level->isCapacityReachedAtRequired()) {
            $builder->add('capacityReachedAt', DateTimeType::class, [
                'label' => 'app.admin.schools.general.capacity_reached_at',
                'required' => true,
                'years' => range($year->getStartYear()-1, $year->getEndYear()),
                'attr' => [
                    'class' => 'field-datetime',
                ],
            ]);
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (PreSetDataEvent $event) {
            $data = $event->getData();
            if (!$data instanceof SchoolEducation) {
                return;
            }

            if (null === $data->getCapacityReachedAt()) {
                $data->setCapacityReachedAt(new \DateTime());
            }
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['level'] = $options['level'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolEducation::class,
            'year' => null,
            'level' => null,
            'block_prefix' => 'school_numbers_collection_item',
        ]);
    }
}
