<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Entity\SchoolEducationUnderrepresentedGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolEducationUnderrepresentedGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description', TextType::class);

        $builder->add('studentSeatsPercentage', NumberType::class, [
            'label' => 'app.admin.schools.general.indicator_student_seats_percentage',
        ]);

        $builder->add('studentSeatsTaken', NumberType::class, [
            'label' => 'app.admin.schools.general.indicator_student_seats_percentage',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SchoolEducationUnderrepresentedGroup::class,
            'allow_add' => true,
            'allow_delete' => true,
            'row_attr' => [
                'class' => 'field-collection',
            ],
            'block_prefix' => 'school_education_underrepresented_group_item',
        ]);
    }
}
