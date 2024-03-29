<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\Review\Event\ReviewCommitChanged;
use App\Domain\Review\Event\ReviewNeedsCheck;
use Symfony\Component\Messenger\MessageBusInterface;

final class ReviewCommitChangedHandler
{
    private $eventPublisher;
    private $eventBus;

    public function __construct(EventPublisherInterface $eventPublisher, MessageBusInterface $eventBus)
    {
        $this->eventPublisher = $eventPublisher;
        $this->eventBus = $eventBus;
    }

    public function __invoke(ReviewCommitChanged $reviewCommitChanged): void
    {
        $this->eventPublisher->publish('/api/reviews', $reviewCommitChanged->getReviewId(), $reviewCommitChanged);

        $this->eventBus->dispatch(new ReviewNeedsCheck(
                $reviewCommitChanged->getReviewId(),
                $reviewCommitChanged->getNewCommitHash(),
                $reviewCommitChanged->getEnabledChecks())
        );
    }
}
