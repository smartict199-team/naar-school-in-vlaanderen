<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Entity\SchoolEducation;
use App\Entity\SchoolFormType;
use App\Entity\SchoolGrade;
use App\Entity\SchoolLevel;
use App\Entity\SchoolPhase;
use App\Entity\SchoolType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class SchoolEducationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $level = $options['level'];
        if (!$level instanceof SchoolLevel) {
            throw new \RuntimeException('level is required');
        }

        if ($level->isAdministrativeGroupsRequired()) {
            $builder->add('administrativeGroups', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'app.admin.schools.educations.secondary_education.administrative_groups_placeholder',
                ],
                'constraints' => [
                    new Regex('/^(\d*,?)*$/', 'OOppppsss'),
                ],
            ]);
        }

        if ($level->isNameRequired()) {
            $builder->add('name', TextType::class, ['required' => false]);
        }

        if ($level->isTypeRequired()) {
            $builder->add('type', SchoolTypeType::class, [
                'level' => $level,
            ]);
        }

        if ($level->isFinalityRequired()) {
            $builder->add('finality', SchoolFinalityType::class, [
                'level' => $level,
            ]);
        }

        if ($level->isFormTypeRequired()) {
            $builder->add('formType', SchoolFormTypeType::class, [
                'level' => $level,
            ]);
        }

        if ($level->isGradeRequired()) {
            $builder->add('grade', SchoolGradeType::class, [
                'year' => $options['year'],
                'level' => $level,
            ]);
        }

        if ($level->isPhaseRequired()) {
            $builder->add('phase', SchoolPhaseType::class, [
                'level' => $level,
            ]);
        }

        $builder->add('position', HiddenType::class);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (PostSetDataEvent $event) use ($level) {
            $data = $event->getData();
            $form = $event->getForm();
            if (!$data instanceof SchoolEducation || $data->isDeletable()) {
                return;
            }

            if ($level->isTypeRequired()) {
                $form->remove('type');
                $form->add('type', SchoolTypeType::class, [
                    'choice_filter' => fn (?SchoolType $type) => $data->getType() === $type,
                    'level' => $level,
                ]);
            }

            if ($level->isFormTypeRequired()) {
                $form->remove('formType');
                $form->add('formType', SchoolFormTypeType::class, [
                    'choice_filter' => fn (?SchoolFormType $type) => $data->getFormType() === $type,
                    'level' => $level,
                ]);
            }

            if ($level->isGradeRequired()) {
                $form->remove('grade');
                $form->add('grade', SchoolGradeType::class, [
                    'choice_filter' => fn (?SchoolGrade $grade) => $data->getGrade() === $grade,
                    'level' => $level,
                ]);
            }

            if ($level->isPhaseRequired()) {
                $form->remove('phase');
                $form->add('phase', SchoolPhaseType::class, [
                    'choice_filter' => fn (?SchoolPhase $phase) => $data->getPhase() === $phase,
                    'level' => $level,
                ]);
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
            'block_prefix' => 'school_education_collection_item',
        ]);
    }
}
