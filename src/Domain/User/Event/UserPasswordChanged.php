<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

final class UserPasswordChanged implements EventInterface
{
    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $newPasswordHash;

    /**
     * @param string $userId
     * @param string $newPasswordHash
     */
    public function __construct(string $userId, string $newPasswordHash)
    {
        $this->userId = $userId;
        $this->newPasswordHash = $newPasswordHash;
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
    public function getNewPasswordHash(): string
    {
        return $this->newPasswordHash;
    }

    public function getEventType(): string
    {
        return 'user_password_changed';
    }
}
