<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Entity\SchoolPhase;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolPhaseType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'multiple' => false,
            'expanded' => false,
            'required' => true,
            'class' => SchoolPhase::class,
            'level' => null,
            'choice_label' => 'name',
            'query_builder' => static function (Options $options) {
                return static function (EntityRepository $repository) use ($options) {
                    $qb = $repository->createQueryBuilder('schoolPhase');

                    $qb->join('schoolPhase.levelPhases', 'levelPhases');
                    $qb->orderBy('levelPhases.position', 'ASC');

                    $level = $options['level'];
                    if (null !== $level) {
                        $qb->andWhere('levelPhases.schoolLevel = :level')
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
