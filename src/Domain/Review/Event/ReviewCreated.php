<?php

declare(strict_types=1);

namespace App\Domain\Review\Event;

use App\Domain\Review\Review;

class ReviewCreated implements EventInterface
{
    /**
     * @var string
     */
    private $reviewId;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $gitRepositoryUrl;

    /**
     * @var string
     */
    private $currentCommitHash;

    /**
     * @param string $reviewId
     * @param string $userId
     * @param string $gitRepositoryUrl
     * @param string $currentCommitHash
     */
    public function __construct(string $reviewId, string $userId, string $gitRepositoryUrl, string $currentCommitHash)
    {
        $this->reviewId = $reviewId;
        $this->userId = $userId;
        $this->gitRepositoryUrl = $gitRepositoryUrl;
        $this->currentCommitHash = $currentCommitHash;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
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
        return 'review_created';
    }
}
