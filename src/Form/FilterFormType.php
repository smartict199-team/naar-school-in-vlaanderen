<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\SchoolLevel;
use App\Entity\SchoolYear;
use App\Form\EventSubscriber\PrePrimaryFormEventSubscriber;
use App\Form\EventSubscriber\PrimaryFormEventSubscriber;
use App\Form\EventSubscriber\SecondaryFormEventSubscriber;
use App\Model\Form\FilterForm;
use App\Model\Form\SchoolCityChoice;
use App\Service\Provider\FilterFormDataProvider;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterFormType extends AbstractType
{
    private FilterFormDataProvider $filterFormDataProvider;

    public function __construct(FilterFormDataProvider $filterFormDataProvider)
    {
        $this->filterFormDataProvider = $filterFormDataProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('cities', ChoiceType::class, [
            'multiple' => true,
            'choices' => $this->filterFormDataProvider->getCityFilterChoices(),
            'label' => 'app.index.form.label.city',
            'attr' => [
                'class' => 'custom-select custom-select-lg ms-list-1',
            ],
            'choice_value' => function ($choice) {
                if ($choice instanceof SchoolCityChoice) {
                    return sprintf('%s-%s-%s', $choice->getPostalCode(), $choice->getRegion() ?? '', $choice->getCity());
                }

                return $choice;
            },
        ]);

        $schoolYearChoices = $this->filterFormDataProvider->getSchoolYearFilterChoices();
        $builder->add('schoolYear', EntityType::class, [
            'label' => 'app.index.form.label.school_year',
            'class' => SchoolYear::class,
            'expanded' => true,
            'choices' => $schoolYearChoices,
            'choice_label' => function (SchoolYear $schoolYear) {
                return sprintf('%s - %s', (string) $schoolYear->getStartYear(), (string) $schoolYear->getEndYear());
            },
        ]);

        $builder->add('level', ChoiceType::class, [
            'label' => 'app.index.form.label.school_level',
            'attr' => ['class' => 'btn-group btn-group-toggle'],
            'block_prefix' => 'school_level',
            'expanded' => true,
            'choices' => [
                'app.index.form.level.regular_education' => SchoolLevel::LEVEL_REGULAR_EDUCATION,
                'app.index.form.level.special_needs_education' => SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION,
                'app.index.form.level.reception_education' => SchoolLevel::LEVEL_RECEPTION_EDUCATION,
            ],
        ]);

        $builder->add('type', ChoiceType::class, [
            'label' => 'app.index.form.label.type',
            'expanded' => true,
            'choices' => [
                'app.index.form.type.pre_primary_education' => SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION,
                'app.index.form.type.primary_education' => SchoolLevel::TYPE_PRIMARY_EDUCATION,
                'app.index.form.type.secondary_education' => SchoolLevel::TYPE_SECONDARY_EDUCATION,
            ],
        ]);

        $builder->addEventSubscriber(new SecondaryFormEventSubscriber($this->filterFormDataProvider));
        $builder->addEventSubscriber(new PrimaryFormEventSubscriber($this->filterFormDataProvider));
        $builder->addEventSubscriber(new PrePrimaryFormEventSubscriber($this->filterFormDataProvider));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FilterForm::class,
            'validation_groups' => false,
        ]);
    }
}
