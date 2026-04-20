<?php

declare(strict_types=1);

namespace App\Entity\User;

use App\Model\LoggableUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_user_api_user")
 */
class ApiUser implements UserInterface, LoggableUserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private ?string $apiToken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(?string $apiToken): void
    {
        $this->apiToken = $apiToken;
    }

    public function getRoles()
    {
        return ['ROLE_API'];
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    public function getLoggableUserName(): string
    {
        return sprintf('API %s', $this->id);
    }

    public function getUsername()
    {
        return $this->id;
    }

    public function eraseCredentials()
    {
    }
}
