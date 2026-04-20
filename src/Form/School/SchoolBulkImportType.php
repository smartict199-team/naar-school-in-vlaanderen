<?php

namespace App\Form\School;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use function Deployer\add;

class SchoolBulkImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('postcode', TextType::class, [
                'label' => 'Postcode',
                'required' => true,
            ])
            ->add('gemeente', TextType::class, [
                'label' => 'Gemeente',
                'required' => true,
            ])
            ->add('onderwijsvormen', ChoiceType::class, [
                'label' => 'Onderwijsvormen',
                'choices' => [
                    'Kleuteronderwijs' => 'pre_primary_education',
                    'Lager Onderwijs' => 'only_primary_education',
                    'Kleuter- en Lager Onderwijs' => 'primary_education',
                    'Secundair Onderwijs' => 'secondary_education',
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => true,
            ])
            ->add('schooljaren', ChoiceType::class, [
                'label' => 'Schooljaren',
                'choices' => [
                    '2024-2025' => '6',
                    '2025-2026' => '7',
                    ],
                'expanded' => true,
                'multiple' => true,
                'required' => true,
            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}

