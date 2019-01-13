<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Document\ReviewDocument;

final class CreateReviewCommand
{
    /**
     * @var string
     */
    private $id;

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
    private $currentCommitHash;

    /**
     * @param string $id
     * @param string $ownerId
     * @param string $gitRepositoryUrl
     * @param string $currentCommitHash
     */
    public function __construct(string $id, string $ownerId, string $gitRepositoryUrl, string $currentCommitHash)
    {
        $this->id = $id;
        $this->ownerId = $ownerId;
        $this->gitRepositoryUrl = $gitRepositoryUrl;
        $this->currentCommitHash = $currentCommitHash;
    }

    public static function fromReviewDocument(ReviewDocument $document): self
    {
        return new self(
            $document->id,
            $document->userId,
            $document->gitRepositoryUrl,
            $document->currentCommitHash
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
    public function getCurrentCommitHash(): string
    {
        return $this->currentCommitHash;
    }
}
