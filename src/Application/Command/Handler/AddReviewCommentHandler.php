<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\AddReviewCommentCommand;
use App\Domain\Review\Event\ReviewCommentCreated;
use App\Domain\Review\Review;
use App\Domain\Review\ReviewEventNormalizerInterface;
use App\Domain\Review\ReviewEventStore;
use App\Domain\Review\ReviewId;
use App\Domain\Review\ReviewRepositoryInterface;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class AddReviewCommentHandler
{
    private $entityManager;
    private $eventBus;
    private $reviewRepository;
    private $normalizer;
    private $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
        ReviewRepositoryInterface $reviewRepository,
        UserRepositoryInterface $userRepository,
        ReviewEventNormalizerInterface $normalizer
    ) {
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
        $this->reviewRepository = $reviewRepository;
        $this->normalizer = $normalizer;
        $this->userRepository = $userRepository;
    }

    public function __invoke(AddReviewCommentCommand $command): void
    {
        $event = new ReviewCommentCreated(
            $command->getReviewId(),
            $command->getCommentId(),
            $command->getAuthorId(),
            $command->getContent(),
            $command->getCreatedAt()
        );

        /** @var Review $review */
        $review = $this->reviewRepository->findById(
            ReviewId::fromString($event->getReviewId())
        );

        /** @var User $user */
        $user = $this->userRepository->findById(
            UserId::fromString($event->getAuthorId())
        );

        $this->entityManager->transactional(function () use ($review, $event, $user): void {
            $review->addComment($event, $user);
            $reviewEvent = ReviewEventStore::fromEvent($event, $review, $this->normalizer);
            $this->entityManager->persist($reviewEvent);
        });

        $this->entityManager->clear();
        $this->eventBus->dispatch($event);
    }
}
