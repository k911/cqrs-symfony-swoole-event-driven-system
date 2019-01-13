<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\User\Event\UserEmailChanged;

final class UserEmailChangedHandler
{
    private $eventPublisher;

    public function __construct(EventPublisherInterface $eventPublisher)
    {
        $this->eventPublisher = $eventPublisher;
    }

    public function __invoke(UserEmailChanged $userEmailChanged): void
    {
        $this->eventPublisher->publish('/api/users', $userEmailChanged->getUserId(), $userEmailChanged);
    }
}
