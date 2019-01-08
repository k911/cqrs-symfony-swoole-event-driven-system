<?php

declare(strict_types=1);

namespace App\Domain\Review\Event;

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
    private $commitHash;

    /**
     * @param string $reviewId
     * @param string $userId
     * @param string $gitRepositoryUrl
     * @param string $commitHash
     */
    public function __construct(string $reviewId, string $userId, string $gitRepositoryUrl, string $commitHash)
    {
        $this->reviewId = $reviewId;
        $this->userId = $userId;
        $this->gitRepositoryUrl = $gitRepositoryUrl;
        $this->commitHash = $commitHash;
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
        return 'review_created';
    }
}
