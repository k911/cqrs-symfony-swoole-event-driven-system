<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\CreateUserCommand;
use App\Domain\User\Event\UserCreated;
use App\Domain\User\User;
use App\Domain\User\UserEventStore;
use App\Domain\User\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreateUserHandler
{
    private $entityManager;
    private $eventBus;
    private $normalizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
        NormalizerInterface $normalizer
    ) {
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
        $this->normalizer = $normalizer;
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $event = new UserCreated(
            $command->getId(),
            $command->getEmail(),
            $command->getPasswordHash(),
            $command->getRoles()
        );

        $this->entityManager->transactional(function () use ($event): void {
            $user = User::fromEvent(
                UserId::fromString($event->getUserId()),
                $event
            );
            $userEvent = UserEventStore::fromEvent($event, $user, $this->normalizer);
            $this->entityManager->persist($user);
            $this->entityManager->persist($userEvent);
        });
        $this->entityManager->clear();
        $this->eventBus->dispatch($event);
    }
}
