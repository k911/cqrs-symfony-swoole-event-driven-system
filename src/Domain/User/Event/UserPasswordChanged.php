<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

final class UserPasswordChanged
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $newPasswordHash;

    /**
     * @param string $id
     * @param string $newPasswordHash
     */
    public function __construct(string $id, string $newPasswordHash)
    {
        $this->id = $id;
        $this->newPasswordHash = $newPasswordHash;
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
    public function getNewPasswordHash(): string
    {
        return $this->newPasswordHash;
    }
}