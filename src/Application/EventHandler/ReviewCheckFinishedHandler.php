<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\Review\Event\ReviewAutomatedChecksStatusChanged;
use App\Domain\Review\Event\ReviewCheckFinished;
use App\Domain\Review\Review;
use App\Domain\Review\ReviewEventStore;
use App\Domain\Review\ReviewId;
use App\Domain\Review\ReviewRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ReviewCheckFinishedHandler
{
    private $eventPublisher;
    private $entityManager;
    private $normalizer;
    private $reviewRepository;
    /**
     * @var MessageBusInterface
     */
    private $eventBus;

    public function __construct(
        EventPublisherInterface $eventPublisher,
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer,
        ReviewRepositoryInterface $reviewRepository,
        MessageBusInterface $eventBus
    ) {
        $this->eventPublisher = $eventPublisher;
        $this->entityManager = $entityManager;
        $this->normalizer = $normalizer;
        $this->reviewRepository = $reviewRepository;
        $this->eventBus = $eventBus;
    }

    public function __invoke(ReviewCheckFinished $reviewCheckFinished): void
    {
        $this->eventPublisher->publish('/api/reviews', $reviewCheckFinished->getReviewId(), $reviewCheckFinished);

        /** @var Review $review */
        $review = $this->reviewRepository->findById(ReviewId::fromString($reviewCheckFinished->getReviewId()));

        $automatedChecksStatus = $review->getAutomatedChecksStatus();

        $this->entityManager->transactional(function () use ($reviewCheckFinished, $review): void {
            $review->apply($reviewCheckFinished);
            $this->entityManager->persist(ReviewEventStore::fromEvent($reviewCheckFinished, $review, $this->normalizer));
        });

        $newAutomatedChecksStatus = $review->getAutomatedChecksStatus();
        if ($automatedChecksStatus !== $newAutomatedChecksStatus) {
            $this->eventBus->dispatch(new ReviewAutomatedChecksStatusChanged(
                $reviewCheckFinished->getReviewId(),
                $newAutomatedChecksStatus
            ));
        }

        $this->entityManager->clear();
    }
}
