<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Model\Form\SchoolEducationsData;
use App\Repository\SchoolLevelRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolEducationsType extends AbstractType
{
    private SchoolLevelRepository $schoolLevelRepository;

    public function __construct(SchoolLevelRepository $schoolLevelRepository)
    {
        $this->schoolLevelRepository = $schoolLevelRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $schoolLevels = $this->schoolLevelRepository->findByLevelsTypes($options['levels'], $options['types']);

        $formTypeVisibleOnFrontend = false;
        $finalityVisibleOnFrontend = false;

        foreach ($schoolLevels as $level) {
            if ($level->isFormTypeRequired()) {
                $formTypeVisibleOnFrontend = true;
            }
            if ($level->isFinalityRequired()) {
                $finalityVisibleOnFrontend = true;
            }
        }

        if ($formTypeVisibleOnFrontend) {
            $builder->add('formTypeVisibleOnFrontend', CheckboxType::class, [
                'label' => 'app.admin.schools.educations.form_types_visible_on_frontend',
                'required' => false,
            ]);
        }

        if ($finalityVisibleOnFrontend) {
            $builder->add('finalityVisibleOnFrontend', CheckboxType::class, [
                'label' => 'app.admin.schools.educations.finalities_visible_on_frontend',
                'required' => false,
            ]);
        }

        $builder->add('educationsCollections', SchoolEducationLevelsType::class, [
            'levels' => $options['levels'],
            'types' => $options['types'],
            'year' => $options['year'],
            'label' => false,
            'block_prefix' => 'educations_collection',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SchoolEducationsData::class,
            'types' => [],
            'levels' => [],
            'year' => null,
        ]);
    }
}
