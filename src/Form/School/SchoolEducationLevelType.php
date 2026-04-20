<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Entity\SchoolLevel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class SchoolEducationLevelType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => SchoolEducationType::class,
            'level' => null,
            'year' => null,
            'allow_add' => true,
            'allow_delete' => true,
            'block_prefix' => 'school_education_collection',
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
                'year' => $level->isPrePrimaryEducation() ? $options['year'] : null,
            ];
        });

        $resolver->setNormalizer('allow_delete', function (Options $options): bool {
            $level = $options['level'];
            if ($level instanceof SchoolLevel) {
                $max = $level->getMaxEducations();
                if (1 === $max && $level->isDefaultRequired()) {
                    return false;
                }
            }

            return true;
        });

        $resolver->setNormalizer('allow_add', function (Options $options): bool {
            $level = $options['level'];
            if ($level instanceof SchoolLevel) {
                $max = $level->getMaxEducations();
                if (1 === $max && $level->isDefaultRequired()) {
                    return false;
                }
            }

            return true;
        });

        $resolver->setNormalizer('constraints', function (Options $options): array {
            $level = $options['level'];
            if ($level instanceof SchoolLevel) {
                $max = $level->getMaxEducations();
                if (null !== $max) {
                    return [new Count(['max' => $max])];
                }
            }

            return [];
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
