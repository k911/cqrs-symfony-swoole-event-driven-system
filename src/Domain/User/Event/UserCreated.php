<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

final class UserCreated implements EventInterface
{
    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string[]
     */
    private $roles;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @param string   $userId
     * @param string   $email
     * @param string   $passwordHash
     * @param string[] $roles
     */
    public function __construct(string $userId, string $email, string $passwordHash, array $roles)
    {
        $this->userId = $userId;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getEventType(): string
    {
        return 'user_created';
    }
}
