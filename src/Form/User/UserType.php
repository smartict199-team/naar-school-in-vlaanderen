<?php

declare(strict_types=1);

namespace App\Form\User;

use App\Form\School\SchoolAdminType;
use App\Model\Role;
use App\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class UserType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', TextType::class, [
            'label' => 'app.admin.user.create.email',
        ]);

        $builder->add('firstName', TextType::class, [
            'label' => 'app.admin.user.create.first_name',
        ]);

        $builder->add('lastName', TextType::class, [
            'label' => 'app.admin.user.create.last_name',
        ]);

        $currentUser = $this->security->getUser();
        $roleChoices = [];

        if ($currentUser instanceof User) {
            if (Role::ROLE_SUPER_ADMIN === $currentUser->getExternalRole()) {
                $roleChoices[] = ['app.admin.user.create.roles.super_admin' => Role::ROLE_SUPER_ADMIN];
                $roleChoices[] = ['app.admin.user.create.roles.group_admin' => Role::ROLE_GROUP_ADMIN];
            }

            if (Role::ROLE_GROUP_ADMIN === $currentUser->getExternalRole()) {
                $roleChoices[] = ['app.admin.user.create.roles.group_admin' => Role::ROLE_GROUP_ADMIN];
            }

            $roleChoices[] = ['app.admin.user.create.roles.school_admin' => Role::ROLE_SCHOOL_ADMIN];

            $builder->add('externalRole', ChoiceType::class, [
                'choices' => $roleChoices,
                'expanded' => true,
                'label' => 'app.admin.user.create.role',
            ]);

            $builder->add('schools', SchoolAdminType::class, [
                'current_user' => $currentUser,
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
