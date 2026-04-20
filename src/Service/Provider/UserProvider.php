<?php

declare(strict_types=1);

namespace App\Service\Provider;

use App\Model\Auth0Filter;
use App\Service\Auth0\UserRepository;
use App\Service\Pagerfanta\Auth0Adapter;
use Pagerfanta\Pagerfanta;

class UserProvider
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsersPagination(Auth0Filter $filter): Pagerfanta
    {
        $adapter = new Auth0Adapter($this->userRepository, $filter);

        $pagerFanta = new Pagerfanta($adapter);
        $pagerFanta->setMaxPerPage($this->userRepository->getMaxPerPage());

        return $pagerFanta;
    }
}
