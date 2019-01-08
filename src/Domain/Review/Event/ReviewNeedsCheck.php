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
    private $gitRepositoryUrl;

    /**
     * @var string
     */
    private $commitHash;

    public function __construct(string $reviewId, string $gitRepositoryUrl, string $commitHash)
    {
        $this->reviewId = $reviewId;
        $this->gitRepositoryUrl = $gitRepositoryUrl;
        $this->commitHash = $commitHash;
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
}