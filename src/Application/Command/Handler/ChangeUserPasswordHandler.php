<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\ChangeUserPasswordCommand;
use App\Domain\User\Event\UserPasswordChanged;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Uuid\RamseyUuidUserId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ChangeUserPasswordHandler
{
    private $entityManager;

    private $eventBus;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
        UserRepositoryInterface $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
        $this->userRepository = $userRepository;
    }

    public function __invoke(ChangeUserPasswordCommand $command): void
    {
        $event = new UserPasswordChanged(
            $command->getId(),
            $command->getNewPasswordHash(),
            );

        $userId = RamseyUuidUserId::fromString($event->getId());

        /** @var User $user */
        $user = $this->userRepository->findById($userId);

        $this->entityManager->transactional(function () use ($user, $event): void {
            $user->applyChangePassword($event);
        });

        $this->eventBus->dispatch($event);
    }
}
