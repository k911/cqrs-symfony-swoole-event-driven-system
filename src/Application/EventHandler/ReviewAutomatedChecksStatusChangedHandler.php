<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\Review\Event\ReviewAutomatedChecksStatusChanged;

final class ReviewAutomatedChecksStatusChangedHandler
{
    /**
     * @var EventPublisherInterface
     */
    private $eventPublisher;

    public function __construct(EventPublisherInterface $eventPublisher)
    {
        $this->eventPublisher = $eventPublisher;
    }

    public function __invoke(ReviewAutomatedChecksStatusChanged $reviewAutomatedChecksStatusChanged): void
    {
        $this->eventPublisher->publish('/api/reviews', $reviewAutomatedChecksStatusChanged->getReviewId(), $reviewAutomatedChecksStatusChanged);
    }
}
