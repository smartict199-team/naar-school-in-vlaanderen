<?php

declare(strict_types=1);

namespace App\Form\School;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolNumbersLevelType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => SchoolNumbersType::class,
            'level' => null,
            'year' => null,
            'allow_add' => false,
            'allow_delete' => false,
            'block_prefix' => 'school_numbers_collection',
            'row_attr' => [
                'class' => 'field-collection',
            ],
        ]);

        $resolver->setNormalizer('entry_options', function (Options $options): array {
            $level = $options['level'];

            return [
                'label' => false,
                'level' => $level,
                'block_name' => 'entry',
                'year' => $options['year'],
            ];
        });

        $resolver->setNormalizer('label', function (Options $options): string {
            $level = $options['level'];

            return $level->getName();
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['level'] = $options['level'];
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
