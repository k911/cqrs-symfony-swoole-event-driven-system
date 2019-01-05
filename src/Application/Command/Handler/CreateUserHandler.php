<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\CreateUserCommand;
use App\Domain\User\Event\UserCreated;
use App\Domain\User\User;
use App\Infrastructure\Uuid\RamseyUuidUserId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateUserHandler
{
    private $entityManager;

    private $eventBus;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus
    ) {
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $userCreatedEvent = new UserCreated(
            $command->getId(),
            $command->getEmail(),
            $command->getPasswordHash(),
            $command->getRoles()
        );

        $this->entityManager->transactional(function () use ($userCreatedEvent): void {
            $userId = RamseyUuidUserId::fromString($userCreatedEvent->getId());
            $this->entityManager->persist(User::fromUserCreatedEvent(
                $userId,
                $userCreatedEvent
            ));
        });

        $this->eventBus->dispatch($userCreatedEvent);
    }
}
