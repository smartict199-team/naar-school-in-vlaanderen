<?php

declare(strict_types=1);

namespace App\Service\Auth0;

use App\Entity\School;
use App\Model\Auth0Filter;
use App\Model\MetaData;
use App\Model\Role;
use App\Model\User;

class FilterService
{
    /**
     * @var string[]
     */
    private array $roles = [
        Role::ROLE_SUPER_ADMIN,
        Role::ROLE_GROUP_ADMIN,
        Role::ROLE_SCHOOL_ADMIN,
    ];

    /**
     * @var string[][]
     */
    private array $rolesAllowedToViewRoles = [
        Role::ROLE_SUPER_ADMIN => [
            Role::ROLE_SUPER_ADMIN,
            Role::ROLE_GROUP_ADMIN,
            Role::ROLE_SCHOOL_ADMIN,
        ],
        Role::ROLE_GROUP_ADMIN => [
            Role::ROLE_GROUP_ADMIN,
            Role::ROLE_SCHOOL_ADMIN,
        ],
        Role::ROLE_SCHOOL_ADMIN => [
            Role::ROLE_SCHOOL_ADMIN,
        ],
    ];

    public function getFilterString(Auth0Filter $filter): string
    {
        $query = '';

        $user = $filter->getUser();
        if ($user instanceof User) {
            if (Role::ROLE_SUPER_ADMIN !== $user->getExternalRole()) {
                $schools = $user->getSchools()->toArray();

                if (!empty($schools)) {
                    $schoolFilter = $this->getSchoolFilter($user->getSchools()->toArray());
                    $query = sprintf('((%s) OR (%s))', $schoolFilter, sprintf('user_id: %s', $user->getId()));
                } else {
                    $query = sprintf('(%s)', sprintf('user_id: %s', $user->getId()));
                }
            }

            $roleFilter = $this->getRoleFilter($user->getExternalRole() ?? '');
            if (null !== $roleFilter) {
                $query = sprintf('%s AND (%s)', $query, $roleFilter);
            }
        }

        if (null !== $filter->getQuery()) {
            if ('' === $query) {
                $query = $this->getQueryFilter($filter);
            } else {
                $query = sprintf('(%s AND %s)', $query, $this->getQueryFilter($filter));
            }
        }

        return $query;
    }

    private function getQueryFilter(Auth0Filter $filter): string
    {
        $query = $filter->getQuery();
        $resultQuery = '';
        $resultQueryArray = [];

        if (null === $query) {
            return $resultQuery;
        }

        $queryArray = explode(' ', $query);

        foreach ($queryArray as $query) {
            if (\strlen($query) < 3) {
                continue;
            }

            $resultQueryArray[] = sprintf('(email: *%s* OR name: *%s* OR given_name: *%s* OR family_name: *%s*)', $query, $query, $query, $query);
        }

        if (!empty($resultQueryArray)) {
            $resultQuery = implode(' AND ', $resultQueryArray);
        }

        return sprintf('(%s)', $resultQuery);
    }

    /**
     * @param School[] $schools
     */
    private function getSchoolFilter(array $schools): string
    {
        $query = '';

        foreach ($schools as $school) {
            $queryArray[] = sprintf('user_metadata.school:"%s"', $school->getId());
            $queryArray[] = sprintf('user_metadata.school:%s', $school->getId());
        }

        if (!empty($queryArray)) {
            $query = implode(' OR ', $queryArray);
        }

        return $query;
    }

    private function getRoleFilter(string $role): ?string
    {
        $excludedRoles = array_diff($this->roles, $this->rolesAllowedToViewRoles[$role] ?? []);

        $conditions = array_map(function (string $role) {
            return sprintf('-user_metadata.%s:"%s"', MetaData::ROLE_KEY, $role);
        }, $excludedRoles);

        return implode(' AND ', $conditions) ?: null;
    }
}
