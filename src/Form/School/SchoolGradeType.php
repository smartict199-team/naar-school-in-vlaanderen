<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Entity\SchoolGrade;
use App\Repository\SchoolGradeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolGradeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'multiple' => false,
            'expanded' => false,
            'required' => true,
            'class' => SchoolGrade::class,
            'level' => null,
            'year' => null,
            'showOnlyFrontend' => false,
            'choice_label' => 'name',
            'query_builder' => static function (Options $options) {
                return static function (SchoolGradeRepository $gradeRepository) use ($options) {
                    $qb = $gradeRepository->createQueryBuilder('schoolGrade');

                    $year = $options['year'];
                    $filterFrontend = $options['showOnlyFrontend'];

                    if (null !== $year) {
                        $qb->andWhere(':year MEMBER OF schoolGrade.years')
                            ->setParameter('year', $options['year']);
                    }

                    if (true === $filterFrontend) {
                        $qb->andWhere('schoolGrade.visibleFrontend = :visibleFrontend');
                        $qb->setParameter('visibleFrontend', true);
                    }

                    $qb->join('schoolGrade.levelGrades', 'levelGrades');
                    $qb->orderBy('levelGrades.position', 'ASC');

                    $level = $options['level'];
                    if (null !== $level) {
                        $qb->andWhere('levelGrades.schoolLevel = :level')
                            ->setParameter('level', $level);
                    }

                    return $qb;
                };
            },
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
