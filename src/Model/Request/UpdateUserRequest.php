<?php

declare(strict_types=1);

namespace App\Model\Request;

class UpdateUserRequest implements RequestInterface
{
    public const USER_PASSWORD_CONNECTION = 'Naarschoolin';

    private ?string $email = null;
    private ?string $givenName = null;
    private ?string $familyName = null;

    /**
     * @var array{'school'?: list<int>, 'editusers'?: bool, 'editvestigingen'?: bool, 'superadmin'?: bool}
     */
    private array $userMetadata = [];

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(?string $givenName): void
    {
        $this->givenName = $givenName;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(?string $familyName): void
    {
        $this->familyName = $familyName;
    }

    /**
     * @param array{'school'?: list<int>, 'editusers'?: bool, 'editvestigingen'?: bool, 'superadmin'?: bool} $userMetadata
     */
    public function setUserMetadata(array $userMetadata): void
    {
        $this->userMetadata = $userMetadata;
    }

    /**
     * @return array{'school'?: list<int>, 'editusers'?: bool, 'editvestigingen'?: bool, 'superadmin'?: bool}|null
     */
    public function getUserMetadata(): ?array
    {
        return $this->userMetadata;
    }
}
