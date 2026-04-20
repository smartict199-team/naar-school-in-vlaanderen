<?php

declare(strict_types=1);

namespace App\Model\Response;

class UserResponse implements ResponseInterface
{
    private ?string $createdAt = null;
    private ?string $email = null;
    private ?bool $emailVerified = null;
    private ?string $nickname = null;
    private ?string $name = null;
    private ?string $userId = null;
    private ?string $givenName = null;
    private ?string $familyName = null;

    /**
     * @var array<string, string>|null
     */
    private ?array $identities = null;

    /**
     * @var array{'school'?: list<int>, 'editusers'?: int, 'editvestigingen'?: int, 'superadmin'?: int}|null
     */
    private ?array $userMetadata = null;

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function isEmailVerified(): ?bool
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(?bool $emailVerified): void
    {
        $this->emailVerified = $emailVerified;
    }

    /**
     * @return array<string, string>|null
     */
    public function getIdentities(): ?array
    {
        return $this->identities;
    }

    /**
     * @param array<string, string>|null $identities
     */
    public function setIdentities(?array $identities): void
    {
        $this->identities = $identities;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
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
     * @return array{'school'?: list<int>, 'editusers'?: int, 'editvestigingen'?: int, 'superadmin'?: int}|null
     */
    public function getUserMetadata(): ?array
    {
        return $this->userMetadata;
    }

    /**
     * @param array{'school'?: list<int>, 'editusers'?: int, 'editvestigingen'?: int, 'superadmin'?: int}|null $userMetadata
     */
    public function setUserMetadata(?array $userMetadata): void
    {
        $this->userMetadata = $userMetadata;
    }
}
