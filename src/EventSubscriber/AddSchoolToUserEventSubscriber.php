<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\School;
use App\Model\User;
use App\Service\Auth0\UserRepository;
use App\Service\Transformer\UserTransformer;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddSchoolToUserEventSubscriber implements EventSubscriberInterface
{
    private UserRepository $userRepository;
    private UserTransformer $userTransformer;
    private AdminContextProvider $contextProvider;

    public function __construct(UserRepository $userRepository, UserTransformer $userTransformer, AdminContextProvider $contextProvider)
    {
        $this->userRepository = $userRepository;
        $this->userTransformer = $userTransformer;
        $this->contextProvider = $contextProvider;
    }

    /**
     * @return array<class-string, list<string>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => ['addSchoolToUser'],
        ];
    }

    public function addSchoolToUser(AfterEntityPersistedEvent $event): void
    {
        $context = $this->contextProvider->getContext();
        if (null === $context) {
            return;
        }

        $user = $context->getUser();
        $entity = $event->getEntityInstance();
        if (!$user instanceof User || !$entity instanceof School) {
            return;
        }

        $user->addSchool($entity);
        $userId = $user->getId();
        if (null === $userId) {
            return;
        }

        $request = $this->userTransformer->transformModelToUpdateRequest($user);
        $this->userRepository->updateUser($userId, $request);
    }
}
