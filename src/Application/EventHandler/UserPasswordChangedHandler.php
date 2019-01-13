<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\User\Event\UserPasswordChanged;

final class UserPasswordChangedHandler
{
    private $eventPublisher;

    public function __construct(EventPublisherInterface $eventPublisher)
    {
        $this->eventPublisher = $eventPublisher;
    }

    public function __invoke(UserPasswordChanged $userPasswordChanged): void
    {
        $this->eventPublisher->publish('/api/users', $userPasswordChanged->getUserId(), $userPasswordChanged);
    }
}
