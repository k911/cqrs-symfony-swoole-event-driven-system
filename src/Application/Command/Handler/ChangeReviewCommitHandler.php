<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\ChangeReviewCommitCommand;
use App\Domain\Review\Event\ReviewCommitChanged;
use App\Domain\Review\Review;
use App\Domain\Review\ReviewEventNormalizerInterface;
use App\Domain\Review\ReviewEventStore;
use App\Domain\Review\ReviewId;
use App\Domain\Review\ReviewRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ChangeReviewCommitHandler
{
    private $entityManager;
    private $eventBus;
    private $reviewRepository;
    private $normalizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $eventBus,
        ReviewRepositoryInterface $reviewRepository,
        ReviewEventNormalizerInterface $normalizer
    ) {
        $this->entityManager = $entityManager;
        $this->eventBus = $eventBus;
        $this->reviewRepository = $reviewRepository;
        $this->normalizer = $normalizer;
    }

    public function __invoke(ChangeReviewCommitCommand $command): void
    {
        /** @var Review $review */
        $review = $this->reviewRepository->findById(
            ReviewId::fromString($command->getReviewId())
        );

        $event = new ReviewCommitChanged(
            $command->getReviewId(),
            $command->getNewCommitHash(),
            $command->getUserId(),
            $review->getEnabledChecks()
        );

        $this->entityManager->transactional(function () use ($review, $event): void {
            $review->apply($event);
            $userEvent = ReviewEventStore::fromEvent($event, $review, $this->normalizer);
            $this->entityManager->persist($userEvent);
        });

        $this->entityManager->clear();
        $this->eventBus->dispatch($event);
    }
}
