<?php

declare(strict_types=1);

namespace App\Form\School;

use App\Entity\School;
use App\Model\Role;
use App\Repository\SchoolRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolAdminType extends AbstractType
{
    private SchoolRepository $schoolRepository;

    public function __construct(SchoolRepository $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'app.admin.user.create.schools',
            'current_user' => null,
            'class' => School::class,
            'group_by' => 'city',
            'choice_label' => function (School $school) {
                return $school->getName() . ' <span class="d-none">(' . $school->getCity() . ')</span>';
            },
            'multiple' => true,
            'attr' => [
                'class' => 'custom-select custom-select-lg ms-list-1',
            ],
        ]);

        $resolver->setNormalizer('query_builder', function (Options $options) {
            $currentUser = $options['current_user'];

            $qb = $this->schoolRepository->createQueryBuilder('s');

            if (Role::ROLE_SUPER_ADMIN !== $currentUser->getExternalRole()) {
                $schoolIds = array_map(static fn (School $school) => $school->getId(), $currentUser->getSchools()->toArray());
                $qb->andWhere($qb->expr()->in('s.id', $schoolIds));
            }

            return $qb;
        });
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
