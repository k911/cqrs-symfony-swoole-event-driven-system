<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\ChangeReviewCommitCommand;
use App\Domain\Review\Event\ReviewCommitChanged;
use App\Domain\Review\Review;
use App\Domain\Review\ReviewEventStore;
use App\Domain\Review\ReviewId;
use App\Domain\Review\ReviewRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ChangeReviewCommitHandler
{
    private $entityManager;
    private $eventBus;
    private $reviewRepository;
    private $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
        ReviewRepositoryInterface $reviewRepository,
        NormalizerInterface $normalizer
    ) {
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
        $this->reviewRepository = $reviewRepository;
        $this->serializer = $normalizer;
    }

    public function __invoke(ChangeReviewCommitCommand $command): void
    {
        $event = new ReviewCommitChanged($command->getReviewId(), $command->getNewCommitHash(), $command->getUserId());
        $reviewId = ReviewId::fromString($event->getReviewId());

        /** @var Review $review */
        $review = $this->reviewRepository->findById($reviewId);

        $this->entityManager->transactional(function () use ($review, $event): void {
            $review->apply($event);
            $userEvent = ReviewEventStore::fromEvent($event, $review, $this->serializer);
            $this->entityManager->persist($userEvent);
        });

        $this->entityManager->clear();
        $this->eventBus->dispatch($event);
    }
}
