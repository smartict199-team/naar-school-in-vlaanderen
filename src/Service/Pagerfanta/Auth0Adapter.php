<?php

declare(strict_types=1);

namespace App\Service\Pagerfanta;

use App\Model\Auth0Filter;
use App\Service\Auth0\Auth0RepositoryInterface;
use App\Service\Auth0\FilterService;
use App\Service\Users\UserService;
use Pagerfanta\Adapter\AdapterInterface;

class Auth0Adapter implements AdapterInterface
{
    private Auth0RepositoryInterface $repository;
    private Auth0Filter $filter;
    private FilterService $filterService;
    private UserService $userService;

    public function __construct(Auth0RepositoryInterface $repository, Auth0Filter $filter)
    {
        $this->repository = $repository;
        $this->filter = $filter;
        $this->filterService = new FilterService();
        $this->userService = new UserService($this->filter->getUser());
    }

    public function getNbResults(): int
    {
        if($this->filter->getUser()->isAdministrator()){
            return \count($this->repository->findAll($this->filter->getQuery()));
        }

        $users = $this->createAndRunQueries($this->filter->getQuery());

        return \count($users);
    }

    /**
     * @param int $offset
     * @param int $length
     *
     * @return array<array-key, mixed>
     */
    public function getSlice($offset, $length): array
    {
        if($this->filter->getUser()->isAdministrator()){
            $users = $this->repository->findAll($this->filter->getQuery());
        }else{
            $users = $this->createAndRunQueries($this->filter->getQuery());
        }

        return array_slice($users, $this->filter->getPage() * $length, $length);
    }

    private function createAndRunQueries(?string $searchquery): array{
        $queries = $this->userService->createQueries($searchquery);
        $users = [];
        foreach ($queries as $query) {
            $users = array_merge($users, $this->repository->findByQuery($query));
        }
        return array_unique($users, SORT_REGULAR);
    }
}
