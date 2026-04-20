<?php

declare(strict_types=1);

namespace App\Service\Auth0;

use App\Event\UserUpdatedEvent;
use App\Exception\UserExistsException;
use App\Model\Request\CreateUserRequest;
use App\Model\Request\UpdateUserRequest;
use App\Model\Response\UserResponse;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class UserRepository implements Auth0RepositoryInterface
{
    public const MAX_PER_PAGE = 10;

    private Client $client;
    private EventDispatcherInterface $eventDispatcher;
    private CacheInterface $userCache;

    public function __construct(Client $client, EventDispatcherInterface $eventDispatcher, CacheInterface $userCache)
    {
        $this->client = $client;
        $this->eventDispatcher = $eventDispatcher;
        $this->userCache = $userCache;
    }

    public function findById(string $id, bool $avoidCache = false): UserResponse
    {
        if ($avoidCache) {
            $this->userCache->delete(sprintf('user_%s', $id));
        }

        $response = $this->userCache->get(sprintf('user_%s', $id), function (ItemInterface $item) use ($id) {
            $item->expiresAfter(new \DateInterval('PT1H'));

            return $this->client->request('GET', sprintf('/api/v2/users/%s', $id), UserResponse::class);
        });

        if (!$response instanceof UserResponse) {
            throw new \Exception('Incorrect response model returned.');
        }

        return $response;
    }

    /**
     * @return UserResponse[]|null
     */
    public function findAll(?string $query): ?array
    {
        if($query) {
            return $this->findByQuery($query);
        }
        return $this->client->request('GET', '/api/v2/users', sprintf('%s[]', UserResponse::class));
    }

    /**
     * @return UserResponse[]|null
     */
    public function findByQuery(?string $query): ?array
    {
        $queryString = $query ? sprintf('?q=%s', urlencode($query)) : '';
        return $this->client->request(
            'GET',
            sprintf('/api/v2/users%s', $queryString),
            sprintf('%s[]', UserResponse::class)
        );
    }

    public function createUser(CreateUserRequest $userRequest): void
    {
        try {
            $this->client->post('/api/v2/users', $userRequest);
        } catch (ClientException $clientException) {
            if (Response::HTTP_CONFLICT === $clientException->getCode()) {
                throw new UserExistsException();
            }

            throw $clientException;
        }
    }

    public function updateUser(string $userId, UpdateUserRequest $userRequest): void
    {
        $this->client->patch(sprintf('/api/v2/users/%s', $userId), $userRequest);
        $this->eventDispatcher->dispatch(new UserUpdatedEvent($userId, $userRequest));
    }

    public function resetPassword(string $email): void
    {
        $options = [];

        $options['body']['email'] = $email;
        $options['body']['connection'] = 'Naarschoolin';

        $this->client->post('/dbconnections/change_password', null, $options, true);
    }

    public function deleteUser(string $userId): void
    {
        $this->client->delete(sprintf('/api/v2/users/%s', $userId));
    }

    public function getMaxPerPage(): int
    {
        return self::MAX_PER_PAGE;
    }
}
