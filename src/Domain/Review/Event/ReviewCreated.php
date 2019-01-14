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
    private $ownerId;

    /**
     * @var string
     */
    private $gitRepositoryUrl;

    /**
     * @var string
     */
    private $commitHash;

    /**
     * @var string[]
     */
    private $enabledChecks;

    /**
     * @param string $reviewId
     * @param string $ownerId
     * @param string $gitRepositoryUrl
     * @param string $commitHash
     * @param array  $enabledChecks
     */
    public function __construct(string $reviewId, string $ownerId, string $gitRepositoryUrl, string $commitHash, array $enabledChecks)
    {
        $this->reviewId = $reviewId;
        $this->ownerId = $ownerId;
        $this->gitRepositoryUrl = $gitRepositoryUrl;
        $this->commitHash = $commitHash;
        $this->enabledChecks = $enabledChecks;
    }

    /**
     * @return string
     */
    public function getOwnerId(): string
    {
        return $this->ownerId;
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

    /**
     * @return string[]
     */
    public function getEnabledChecks(): array
    {
        return $this->enabledChecks;
    }
}
