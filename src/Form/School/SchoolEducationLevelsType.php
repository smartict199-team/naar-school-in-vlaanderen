<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Repository\SchoolLevelRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolEducationLevelsType extends AbstractType
{
    private SchoolLevelRepository $schoolLevelRepository;

    public function __construct(SchoolLevelRepository $schoolLevelRepository)
    {
        $this->schoolLevelRepository = $schoolLevelRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $schoolLevels = $this->schoolLevelRepository->findByLevelsTypes($options['levels'], $options['types']);
        foreach ($schoolLevels as $level) {
            $builder->add(
                (string) $level->getId(),
                SchoolEducationLevelType::class,
                [
                    'level' => $level,
                    'year' => $options['year'],
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'types' => [],
            'levels' => [],
            'year' => null,
        ]);
    }
}
