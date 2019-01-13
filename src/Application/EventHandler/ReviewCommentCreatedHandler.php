<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\Review\Event\ReviewCommentCreated;

final class ReviewCommentCreatedHandler
{
    private $eventPublisher;

    public function __construct(EventPublisherInterface $eventPublisher)
    {
        $this->eventPublisher = $eventPublisher;
    }

    public function __invoke(ReviewCommentCreated $reviewCommentCreated): void
    {
        $this->eventPublisher->publish('/api/reviews', $reviewCommentCreated->getReviewId(), $reviewCommentCreated);
        $this->eventPublisher->publish('/api/review-comments', $reviewCommentCreated->getCommentId(), $reviewCommentCreated);
    }
}
