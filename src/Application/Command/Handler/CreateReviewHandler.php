<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\CreateReviewCommand;
use App\Domain\Review\Event\ReviewCreated;
use App\Domain\Review\Review;
use App\Domain\Review\ReviewEventStore;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreateReviewHandler
{
    private $entityManager;
    private $eventBus;
    private $normalizer;
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
        NormalizerInterface $normalizer,
        UserRepositoryInterface $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
        $this->normalizer = $normalizer;
        $this->userRepository = $userRepository;
    }

    public function __invoke(CreateReviewCommand $command): void
    {
        $event = new ReviewCreated(
            $command->getId(),
            $command->getUserId(),
            $command->getGitRepositoryUrl(),
            $command->getCurrentCommitHash()
        );

        /** @var User $user */
        $user = $this->userRepository->findById(UserId::fromString($command->getUserId()));

        $this->entityManager->transactional(function () use ($event, $user): void {
            $review = Review::fromEvent($event, $user);
            $reviewEvent = ReviewEventStore::fromEvent($event, $review, $this->normalizer);
            $this->entityManager->persist($review);
            $this->entityManager->persist($reviewEvent);
        });
        $this->entityManager->clear();
        $this->eventBus->dispatch($event);
    }
}
