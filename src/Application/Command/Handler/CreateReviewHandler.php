<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\CreateReviewCommand;
use App\Domain\Review\Event\ReviewCreated;
use App\Domain\Review\Review;
use App\Domain\Review\ReviewEventNormalizerInterface;
use App\Domain\Review\ReviewEventStore;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CreateReviewHandler
{
    private $entityManager;
    private $eventBus;
    private $normalizer;
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
        ReviewEventNormalizerInterface $normalizer,
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
            $command->getOwnerId(),
            $command->getGitRepositoryUrl(),
            $command->getCurrentCommitHash()
        );

        /** @var User $user */
        $user = $this->userRepository->findById(UserId::fromString($command->getOwnerId()));

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
