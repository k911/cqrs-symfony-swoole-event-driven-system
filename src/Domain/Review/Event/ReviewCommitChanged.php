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
     * @var string[]
     */
    private $enabledChecks;

    /**
     * @param string $reviewId
     * @param string $newCommitHash
     * @param string $userId
     * @param array  $enabledChecks
     */
    public function __construct(string $reviewId, string $newCommitHash, string $userId, array $enabledChecks)
    {
        $this->reviewId = $reviewId;
        $this->newCommitHash = $newCommitHash;
        $this->userId = $userId;
        $this->enabledChecks = $enabledChecks;
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

    /**
     * @return string[]
     */
    public function getEnabledChecks(): array
    {
        return $this->enabledChecks;
    }
}
