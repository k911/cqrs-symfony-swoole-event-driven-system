<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\User\Event\UserCreated;

final class UserCreatedHandler
{
    private $eventPublisher;

    public function __construct(EventPublisherInterface $eventPublisher)
    {
        $this->eventPublisher = $eventPublisher;
    }

    public function __invoke(UserCreated $userCreated): void
    {
        $this->eventPublisher->publish('/api/users', $userCreated->getUserId(), $userCreated);
    }
}
