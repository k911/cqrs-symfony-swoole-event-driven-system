<?php

declare(strict_types=1);

namespace App\Domain\User\Event;

final class UserEmailChanged implements EventInterface
{
    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $newEmail;

    /**
     * @param string $userId
     * @param string $newEmail
     */
    public function __construct(string $userId, string $newEmail)
    {
        $this->userId = $userId;
        $this->newEmail = $newEmail;
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
    public function getNewEmail(): string
    {
        return $this->newEmail;
    }

    public function getEventType(): string
    {
        return 'user_email_changed';
    }
}
