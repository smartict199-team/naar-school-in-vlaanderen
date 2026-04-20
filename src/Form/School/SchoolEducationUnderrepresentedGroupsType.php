<?php

declare(strict_types=1);

namespace App\Form\School;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolEducationUnderrepresentedGroupsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('underrepresentedGroup', SchoolEducationUnderrepresentedGroupType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entry_type' => SchoolEducationUnderrepresentedGroupType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'row_attr' => [
                'class' => 'field-collection',
            ],
        ]);
    }

    public function getParent()
    {
        return CollectionType::class;
    }
}
