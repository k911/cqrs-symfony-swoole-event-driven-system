<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

final class UserCreated
{
    /**
     * @var string
     */
    private $id;

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
     * @param string   $id
     * @param string   $email
     * @param string   $passwordHash
     * @param string[] $roles
     */
    public function __construct(string $id, string $email, string $passwordHash, array $roles)
    {
        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
}
