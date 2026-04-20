<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Repository\SchoolLevelRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolNumbersLevelsType extends AbstractType
{
    private SchoolLevelRepository $schoolLevelRepository;

    public function __construct(SchoolLevelRepository $schoolLevelRepository)
    {
        $this->schoolLevelRepository = $schoolLevelRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $year = $options['year'];
        $schoolLevels = $this->schoolLevelRepository->findByLevelsTypes($options['levels'], $options['types']);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (PreSetDataEvent $event) use ($year, $schoolLevels) {
            foreach ($event->getData() as $levelId => $schoolEducations) {
                $level = $this->schoolLevelRepository->find($levelId);
                if (\in_array($level, $schoolLevels, true)) {
                    $event->getForm()->add((string) $levelId, SchoolNumbersLevelType::class, [
                        'level' => $level,
                        'year' => $year,
                    ]);
                }
            }
        });
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
