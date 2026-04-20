<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Entity\SchoolYear;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SchoolEducationsCloneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('year', EntityType::class, [
            'label' => 'app.admin.school_year.title',
            'class' => SchoolYear::class,
            'choices' => $options['schoolYears'],
            'constraints' => new NotBlank(),
        ]);

        $builder->add('confirm', CheckboxType::class, [
            'label' => 'app.admin.schools.educations.copy.form.confirm',
            'label_attr' => [
                'class' => 'text-left',
            ],
            'constraints' => new NotBlank(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'schoolYears' => null,
        ]);
    }
}
