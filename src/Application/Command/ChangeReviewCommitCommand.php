<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Document\ReviewChangeCommitDocument;

class ChangeReviewCommitCommand
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

    public static function fromDocument(ReviewChangeCommitDocument $document): self
    {
        return new self(
            $document->id,
            $document->newCommitHash,
            $document->userId
        );
    }

    /**
     * @return string
     */
    public function getReviewId(): string
    {
        return $this->reviewId;
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
}
