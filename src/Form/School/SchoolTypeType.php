<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Entity\SchoolType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolTypeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'multiple' => false,
            'expanded' => false,
            'required' => false,
            'placeholder' => '',
            'class' => SchoolType::class,
            'level' => null,
            'only_visible_frontend' => false,
            'choice_label' => 'name',
            'query_builder' => static function (Options $options) {
                return static function (EntityRepository $repository) use ($options) {
                    $qb = $repository->createQueryBuilder('schoolType');

                    $level = $options['level'];
                    if (null !== $level) {
                        $qb->join('schoolType.levelTypes', 'levelTypes')
                            ->andWhere('levelTypes.schoolLevel = :level')
                            ->setParameter('level', $level);
                    }

                    $visibleFrontend = $options['only_visible_frontend'];
                    if ($visibleFrontend) {
                        $qb->andWhere('schoolType.visibleFrontend = :visibleFrontend')
                            ->setParameter('visibleFrontend', $visibleFrontend);
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
