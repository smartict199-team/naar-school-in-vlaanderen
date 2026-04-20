<?php

declare(strict_types=1);

namespace App\Service\Auth0;

use App\Model\Response\ResponseInterface;

interface Auth0RepositoryInterface
{
    /**
     * @return ResponseInterface[]|null
     */
    public function findAll(?string $query): ?array;

    /**
     * @return ResponseInterface[]|null
     */
    public function findByQuery(?string $query): ?array;

    public function getMaxPerPage(): int;

}
