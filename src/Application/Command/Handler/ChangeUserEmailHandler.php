<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\ChangeUserEmailCommand;
use App\Domain\User\Event\UserEmailChanged;
use App\Domain\User\User;
use App\Domain\User\UserEventNormalizerInterface;
use App\Domain\User\UserEventStore;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ChangeUserEmailHandler
{
    private $entityManager;
    private $eventBus;
    private $userRepository;
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
        UserRepositoryInterface $userRepository,
        UserEventNormalizerInterface $normalizer
    ) {
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
        $this->userRepository = $userRepository;
        $this->serializer = $normalizer;
    }

    public function __invoke(ChangeUserEmailCommand $command): void
    {
        $event = new UserEmailChanged($command->getId(), $command->getNewEmail());
        $userId = UserId::fromString($event->getUserId());

        /** @var User $user */
        $user = $this->userRepository->findById($userId);

        $this->entityManager->transactional(function () use ($user, $event): void {
            $user->changeEmail($event);
            $userEvent = UserEventStore::fromEvent($event, $user, $this->serializer);
            $this->entityManager->persist($userEvent);
        });

        $this->entityManager->clear();
        $this->eventBus->dispatch($event);
    }
}
