<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\School;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends OAuthUser implements EquatableInterface, LoggableUserInterface
{
    private ?string $id = null;
    private ?string $firstName = null;
    private ?string $lastName = null;
    private ?string $email = null;
    private ?string $password = null;
    private ?string $externalRole = null;
    private ?\DateTime $createdAt = null;

    /**
     * @var array<array-key, string>
     */
    private array $roles = ['ROLE_USER', 'ROLE_OAUTH_USER'];

    /**
     * @var Collection<int, School>
     */
    private Collection $schools;

    public function __construct(string $username)
    {
        parent::__construct($username);
        $this->username = $username;
        $this->schools = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return Collection<int, School>
     */
    public function getSchools(): Collection
    {
        return $this->schools;
    }

    /**
     * @param Collection<int, School> $schools
     */
    public function setSchools(Collection $schools): void
    {
        $this->schools = $schools;
    }

    public function addSchool(School $school): void
    {
        $this->schools->add($school);
    }

    /**
     * @return array<array-key, string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array|string[]  $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function addRole(string $role): void
    {
        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function isEqualTo(UserInterface $user)
    {
        return $user->getUsername() === $this->username;
    }

    public function getExternalRole(): ?string
    {
        return $this->externalRole;
    }

    public function setExternalRole(?string $externalRole): void
    {
        $this->externalRole = $externalRole;
    }

    public function getLoggableUserName(): string
    {
        return $this->email;
    }

    public function isAdministrator(): bool
    {
        return \in_array(Role::ROLE_SUPER_ADMIN, $this->roles, true);
    }
}
