<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Command\StartReviewCheckCommand;
use App\Application\Contract\EventPublisherInterface;
use App\Domain\Review\AutomatedCheck;
use App\Domain\Review\Event\ReviewNeedsCheck;
use App\Domain\Review\Review;
use App\Domain\Review\ReviewEventNormalizerInterface;
use App\Domain\Review\ReviewEventStore;
use App\Domain\Review\ReviewId;
use App\Domain\Review\ReviewRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ReviewNeedsCheckHandler
{
    private $eventPublisher;
    private $entityManager;
    private $normalizer;
    private $reviewRepository;
    private $commandBus;

    public function __construct(
        EventPublisherInterface $eventPublisher,
        MessageBusInterface $commandBus,
        EntityManagerInterface $entityManager,
        ReviewEventNormalizerInterface $normalizer,
        ReviewRepositoryInterface $reviewRepository
    ) {
        $this->eventPublisher = $eventPublisher;
        $this->entityManager = $entityManager;
        $this->normalizer = $normalizer;
        $this->reviewRepository = $reviewRepository;
        $this->commandBus = $commandBus;
    }

    public function __invoke(ReviewNeedsCheck $reviewNeedsCheck): void
    {
        $this->eventPublisher->publish('/api/reviews', $reviewNeedsCheck->getReviewId(), $reviewNeedsCheck);

        /** @var Review $review */
        $review = $this->reviewRepository->findById(ReviewId::fromString($reviewNeedsCheck->getReviewId()));

        $this->entityManager->transactional(function () use ($reviewNeedsCheck, $review): void {
            $review->apply($reviewNeedsCheck);
            $this->entityManager->persist(ReviewEventStore::fromEvent($reviewNeedsCheck, $review, $this->normalizer));
        });

        $this->commandBus->dispatch(new StartReviewCheckCommand(
            $reviewNeedsCheck->getReviewId(),
            AutomatedCheck::CHECK_NAME_PHPSTAN,
            $review->getGitRepositoryUrl(),
            $reviewNeedsCheck->getCommitHash()
        ));

        $this->commandBus->dispatch(new StartReviewCheckCommand(
            $reviewNeedsCheck->getReviewId(),
            AutomatedCheck::CHECK_NAME_PHPCSFIXER,
            $review->getGitRepositoryUrl(),
            $reviewNeedsCheck->getCommitHash()
        ));

        $this->entityManager->clear();
    }
}
