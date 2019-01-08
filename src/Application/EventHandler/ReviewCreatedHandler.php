<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\Review\Event\ReviewCreated;
use App\Domain\Review\Event\ReviewNeedsCheck;
use App\Domain\Review\Review;
use App\Domain\Review\ReviewEventStore;
use App\Domain\Review\ReviewId;
use App\Domain\Review\ReviewRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ReviewCreatedHandler
{
    private $eventPublisher;
    private $eventBus;
    private $entityManager;
    private $normalizer;
    private $reviewRepository;

    public function __construct(
        EventPublisherInterface $eventPublisher,
        MessageBusInterface $eventBus,
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer,
        ReviewRepositoryInterface $reviewRepository
    )
    {
        $this->eventPublisher = $eventPublisher;
        $this->eventBus = $eventBus;
        $this->entityManager = $entityManager;
        $this->normalizer = $normalizer;
        $this->reviewRepository = $reviewRepository;
    }

    public function __invoke(ReviewCreated $reviewCreated): void
    {
        $this->eventPublisher->publish('/api/reviews', $reviewCreated->getReviewId(), $reviewCreated);

        $event = new ReviewNeedsCheck($reviewCreated->getReviewId(), $reviewCreated->getGitRepositoryUrl(), $reviewCreated->getCurrentCommitHash());

        /** @var Review $review */
        $review = $this->reviewRepository->findById(ReviewId::fromString($event->getReviewId()));

        $this->entityManager->transactional(function () use ($event, $review): void {
            $this->entityManager->persist(ReviewEventStore::fromEvent($event, $review, $this->normalizer));
        });

        $this->entityManager->clear();
        $this->eventBus->dispatch($event);
    }
}
