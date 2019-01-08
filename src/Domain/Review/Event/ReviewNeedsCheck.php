<?php

declare(strict_types=1);

namespace App\Domain\Review\Event;

class ReviewNeedsCheck implements EventInterface
{
    private $reviewId;

    /**
     * @var string
     */
    private $gitRepositoryUrl;

    /**
     * @var string
     */
    private $currentCommitHash;

    public function __construct(string $reviewId, string $gitRepositoryUrl, string $currentCommitHash)
    {
        $this->reviewId = $reviewId;
        $this->gitRepositoryUrl = $gitRepositoryUrl;
        $this->currentCommitHash = $currentCommitHash;
    }

    /**
     * @return string
     */
    public function getGitRepositoryUrl(): string
    {
        return $this->gitRepositoryUrl;
    }

    /**
     * @return string
     */
    public function getCurrentCommitHash(): string
    {
        return $this->currentCommitHash;
    }

    public function getReviewId(): string
    {
        return $this->reviewId;
    }

    public function getEventType(): string
    {
        return 'review_needs_check';
    }
}
