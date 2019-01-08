<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\Review\Event\ReviewCreated;

final class ReviewCreatedHandler
{
    /**
     * @var EventPublisherInterface
     */
    private $eventPublisher;

    public function __construct(EventPublisherInterface $eventPublisher)
    {
        $this->eventPublisher = $eventPublisher;
    }

    public function __invoke(ReviewCreated $reviewCreated): void
    {
        $this->eventPublisher->publish('/api/reviews', $reviewCreated->getReviewId(), $reviewCreated);
    }
}
