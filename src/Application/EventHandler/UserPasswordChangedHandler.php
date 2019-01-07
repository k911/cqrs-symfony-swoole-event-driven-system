<?php
declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\User\Event\UserPasswordChanged;

final class UserPasswordChangedHandler
{
    /**
     * @var EventPublisherInterface
     */
    private $eventPublisher;

    public function __construct(EventPublisherInterface $eventPublisher)
    {
        $this->eventPublisher = $eventPublisher;
    }

    public function __invoke(UserPasswordChanged $userPasswordChanged)
    {
        $this->eventPublisher->publish('/api/users', $userPasswordChanged->getUserId(), $userPasswordChanged);
    }

}
