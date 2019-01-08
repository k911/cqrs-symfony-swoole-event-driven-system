<?php

declare(strict_types=1);

namespace App\Domain\Review\Event;

class ReviewCommitChanged implements EventInterface
{
    /**
     * @var string
     */
    private $reviewId;

    /**
     * @var string
     */
    private $newCommitHash;

    /**
     * @var string
     */
    private $userId;

    /**
     * @param string $reviewId
     * @param string $newCommitHash
     * @param string $userId
     */
    public function __construct(string $reviewId, string $newCommitHash, string $userId)
    {
        $this->reviewId = $reviewId;
        $this->newCommitHash = $newCommitHash;
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getNewCommitHash(): string
    {
        return $this->newCommitHash;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getReviewId(): string
    {
        return $this->reviewId;
    }

    public function getEventType(): string
    {
        return 'review_commit_changed';
    }
}
