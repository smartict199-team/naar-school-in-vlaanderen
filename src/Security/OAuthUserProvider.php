<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\Response\UserResponse;
use App\Model\User;
use App\Repository\SchoolRepository;
use App\Service\Auth0\UserRepository;
use App\Service\Transformer\UserTransformer;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\CacheInterface;

class OAuthUserProvider extends \HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider
{
    public const NAMESPACE = 'https://naarschoolinvlaanderen.be/';

    private SchoolRepository $schoolRepository;
    private UserRepository $userRepository;
    private CacheInterface $userCache;
    private UserTransformer $transformer;

    public function __construct(SchoolRepository $schoolRepository, UserRepository $userRepository, CacheInterface $userCache, UserTransformer $transformer)
    {
        $this->schoolRepository = $schoolRepository;
        $this->userRepository = $userRepository;
        $this->userCache = $userCache;
        $this->transformer = $transformer;
    }

    /**
     * @param string $username
     */
    public function loadUserByUsername($username): UserInterface
    {
        $response = $this->userRepository->findById($username);

        return $this->createUser($response);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $data = $response->getData();

        return $this->loadUserByUsername($data['sub']);
    }

    public function createUser(UserResponse $response): User
    {
        return $this->transformer->transformResponseToModel($response);
    }

    /**
     * @param class-string<UserInterface> $class
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
