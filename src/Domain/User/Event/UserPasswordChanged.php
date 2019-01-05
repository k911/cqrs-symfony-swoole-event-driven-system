<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

final class UserPasswordChanged
{
    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @param string $passwordHash
     */
    public function __construct(string $passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
}
