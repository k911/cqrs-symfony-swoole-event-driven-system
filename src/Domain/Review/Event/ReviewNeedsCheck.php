<?php

declare(strict_types=1);

namespace App\Domain\Review\Event;

class ReviewNeedsCheck implements EventInterface
{
    /**
     * @var string
     */
    private $reviewId;

    /**
     * @var string
     */
    private $commitHash;
    /**
     * @var string[]
     */
    private $enabledChecks;

    public function __construct(string $reviewId, string $commitHash, array $enabledChecks)
    {
        $this->reviewId = $reviewId;
        $this->commitHash = $commitHash;
        $this->enabledChecks = $enabledChecks;
    }

    /**
     * @return string
     */
    public function getCommitHash(): string
    {
        return $this->commitHash;
    }

    public function getReviewId(): string
    {
        return $this->reviewId;
    }

    public function getEventType(): string
    {
        return 'review_needs_check';
    }

    /**
     * @return string[]
     */
    public function getEnabledChecks(): array
    {
        return $this->enabledChecks;
    }
}
