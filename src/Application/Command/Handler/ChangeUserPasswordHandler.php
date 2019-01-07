<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\ChangeUserPasswordCommand;
use App\Domain\User\Event\UserPasswordChanged;
use App\Domain\User\User;
use App\Domain\User\UserEventStore;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ChangeUserPasswordHandler
{
    private $entityManager;
    private $eventBus;
    private $userRepository;
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
        UserRepositoryInterface $userRepository,
        NormalizerInterface $normalizer
    ) {
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
        $this->userRepository = $userRepository;
        $this->serializer = $normalizer;
    }

    public function __invoke(ChangeUserPasswordCommand $command): void
    {
        $event = new UserPasswordChanged($command->getId(), $command->getNewPasswordHash());
        $userId = UserId::fromString($event->getUserId());

        /** @var User $user */
        $user = $this->userRepository->findById($userId);

        $this->entityManager->transactional(function () use ($user, $event): void {
            $user->applyChangePassword($event);
            $userEvent = UserEventStore::fromEvent($event, $user, $this->serializer);
            $this->entityManager->persist($userEvent);
        });
        $this->entityManager->clear();
        $this->eventBus->dispatch($event);
    }
}
