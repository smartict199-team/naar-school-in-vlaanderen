<?php

declare(strict_types=1);

namespace App\Service\Transformer;

use App\Entity\School;
use App\Model\MetaData;
use App\Model\Request\CreateUserRequest;
use App\Model\Request\UpdateUserRequest;
use App\Model\Response\UserResponse;
use App\Model\User;
use App\Repository\SchoolRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class UserTransformer
{
    public const API_DATE_FORMAT = 'Y-m-d\TH:i:s.u\Z';

    private SchoolRepository $schoolRepository;

    public function __construct(SchoolRepository $schoolRepository)
    {
        $this->schoolRepository = $schoolRepository;
    }

    public function transformModelToCreateRequest(User $user): CreateUserRequest
    {
        $request = new CreateUserRequest();

        $request->setEmail($user->getEmail());
        $request->setFamilyName($user->getLastName());
        $request->setGivenName($user->getFirstName());
        $request->setUserMetadata($this->getUserMetaData($user));
        $request->setPassword(uniqid('nsiv', true));

        return $request;
    }

    public function transformModelToUpdateRequest(User $user): UpdateUserRequest
    {
        $request = new UpdateUserRequest();

        $request->setEmail($user->getEmail());
        $request->setFamilyName($user->getLastName());
        $request->setGivenName($user->getFirstName());
        $request->setUserMetadata($this->getUserMetaData($user));

        return $request;
    }

    /**
     * @param UserResponse[] $responses
     *
     * @return User[]
     */
    public function transformResponseArrayToModels(array $responses): array
    {
        $users = [];

        foreach ($responses as $response) {
            $users[] = $this->transformResponseToModel($response);
        }

        return $users;
    }

    public function transformResponseToModel(UserResponse $response): User
    {
        $model = new User($response->getUserId());

        $model->setId($response->getUserId());
        $model->setFirstName($response->getGivenName());
        $model->setLastName($response->getFamilyName());
        $model->setEmail($response->getEmail());
        $model->setCreatedAt(\DateTime::createFromFormat(self::API_DATE_FORMAT, $response->getCreatedAt() ?? '') ?: null);

        $metaData = $response->getUserMetadata() ?? [];
        $model->setSchools($this->getSchoolsFromMetaData($metaData));

        // $model->setSchoolAdmin(\array_key_exists(MetaData::IS_SCHOOL_ADMIN, $metaData) ? (bool) $metaData[MetaData::IS_SCHOOL_ADMIN] : false);
        // $model->setGroupAdmin(\array_key_exists(MetaData::IS_GROUP_ADMIN, $metaData) ? (bool) $metaData[MetaData::IS_GROUP_ADMIN] : false);
        // $model->setSuperAdmin(\array_key_exists(MetaData::IS_SUPER_ADMIN, $metaData) ? (bool) $metaData[MetaData::IS_SUPER_ADMIN] : false);

        $role = $metaData[MetaData::ROLE_KEY] ?? null;
        if (null !== $role) {
            $model->addRole($role);
            $model->setExternalRole($role);
        }

        return $model;
    }

    /**
     * @param array{'school'?: list<int>, 'editusers'?: int, 'editvestigingen'?: int, 'superadmin'?: int}|null $metaData
     *
     * @return Collection<array-key, School>
     */
    private function getSchoolsFromMetaData(?array $metaData): Collection
    {
        if (!\is_array($metaData) || !\array_key_exists(MetaData::SCHOOL_KEY, $metaData)) {
            return new ArrayCollection();
        }

        $schools = $this->schoolRepository->findBy(['id' => $metaData[MetaData::SCHOOL_KEY] ?? []]);
        if (\count($schools) > 0) {
            return new ArrayCollection($schools);
        }

        return new ArrayCollection();
    }

    /**
     * @return array{'school': list<int>, 'role': string|null}
     */
    private function getUserMetaData(User $user): array
    {
        $metaData = [];

        $metaData[MetaData::SCHOOL_KEY] = $this->getSchoolMetaData($user->getSchools());
        $metaData[MetaData::ROLE_KEY] = $user->getExternalRole();

        return $metaData;
    }

    /**
     * @param Collection<array-key, School> $schools
     *
     * @return list<int>
     */
    private function getSchoolMetaData(Collection $schools): array
    {
        $ids = [];

        foreach ($schools as $school) {
            $schoolId = $school->getId();
            if (null !== $schoolId) {
                $ids[] = $schoolId;
            }
        }

        return $ids;
    }
}
