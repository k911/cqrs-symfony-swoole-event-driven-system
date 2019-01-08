<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\Review\Event\ReviewCreated;
use App\Domain\Review\Event\ReviewNeedsCheck;
use Symfony\Component\Messenger\MessageBusInterface;

final class ReviewCreatedHandler
{
    private $eventPublisher;
    private $eventBus;

    public function __construct(EventPublisherInterface $eventPublisher, MessageBusInterface $eventBus)
    {
        $this->eventPublisher = $eventPublisher;
        $this->eventBus = $eventBus;
    }

    public function __invoke(ReviewCreated $reviewCreated): void
    {
        $this->eventPublisher->publish('/api/reviews', $reviewCreated->getReviewId(), $reviewCreated);

        $this->eventBus->dispatch(new ReviewNeedsCheck(
                $reviewCreated->getReviewId(),
                $reviewCreated->getGitRepositoryUrl(),
                $reviewCreated->getCommitHash())
        );
    }
}
