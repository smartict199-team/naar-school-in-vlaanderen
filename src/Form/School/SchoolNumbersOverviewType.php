<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Model\Form\SchoolNumbersData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolNumbersOverviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('educationsCollections', SchoolNumbersLevelsType::class, [
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
            'data_class' => SchoolNumbersData::class,
            'types' => [],
            'levels' => [],
            'year' => null,
        ]);
    }
}
