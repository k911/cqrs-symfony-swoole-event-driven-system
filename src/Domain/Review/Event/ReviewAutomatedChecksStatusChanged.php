<?php

declare(strict_types=1);

namespace App\Domain\Review\Event;

class ReviewAutomatedChecksStatusChanged implements EventInterface
{
    /**
     * @var string
     */
    private $reviewId;

    /**
     * @var string
     */
    private $newAutomatedChecksStatus;

    /**
     * @param string $reviewId
     * @param string $newAutomatedChecksStatus
     */
    public function __construct(string $reviewId, string $newAutomatedChecksStatus)
    {
        $this->reviewId = $reviewId;
        $this->newAutomatedChecksStatus = $newAutomatedChecksStatus;
    }

    /**
     * @return string
     */
    public function getNewAutomatedChecksStatus(): string
    {
        return $this->newAutomatedChecksStatus;
    }

    public function getReviewId(): string
    {
        return $this->reviewId;
    }

    public function getEventType(): string
    {
        return 'review_automated_checks_status_changed';
    }
}
