<?php

namespace App\Service\Users;

use App\Entity\School;
use App\Model\Role;
use App\Model\User;

class UserService
{
    private ?User $user;

    public function __construct(?User $user)
    {
        $this->user = $user;
    }

    public function getUserSchools(): array
    {
        if (null === $this->user) {
            return [];
        }

        return $this->user->getSchools()->toArray();
    }

    public function createQueries(?string $searchquery): array
    {
        $schools = $this->getUserSchools();
        $schoolIds = array_map(fn($school) => $school->getId(), $schools);
        $maxPerQuery = 100;

        $chunks = array_chunk($schoolIds, $maxPerQuery);

        $queries = array_map(function ($chunk) use ($searchquery) {
            $schoolQuery = implode(' OR ', array_map(fn($schoolId) => 'user_metadata.school:' . $schoolId, $chunk));
            $excludeSuperAdmin = '-user_metadata.role:' . Role::ROLE_SUPER_ADMIN ;
            return $searchquery
                ? sprintf('(%s) AND (%s) AND (%s)', $schoolQuery, $searchquery, $excludeSuperAdmin)
                : sprintf('(%s) AND (%s)', $schoolQuery, $excludeSuperAdmin);
        }, $chunks);

        return $queries;
    }

}
