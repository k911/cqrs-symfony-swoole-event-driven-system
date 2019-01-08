<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\Review\Event\ReviewCheckFinished;

final class CheckFinishedHandler
{
    private $eventPublisher;

    public function __construct(EventPublisherInterface $eventPublisher)
    {
        $this->eventPublisher = $eventPublisher;
    }

    public function __invoke(ReviewCheckFinished $checkFinished): void
    {
        $this->eventPublisher->publish('/api/reviews', $checkFinished->getReviewId(), $checkFinished);

        dump($checkFinished);
    }
}
