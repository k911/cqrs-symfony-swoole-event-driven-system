<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Document\ReviewDocument;
use App\Domain\Review\AutomatedCheck;
use Assert\Assertion;

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
     * @var string[]
     */
    private $enabledChecks;

    /**
     * @param string $id
     * @param string $ownerId
     * @param string $gitRepositoryUrl
     * @param string $currentCommitHash
     * @param array  $enabledChecks
     */
    public function __construct(string $id, string $ownerId, string $gitRepositoryUrl, string $currentCommitHash, array $enabledChecks)
    {
        $this->id = $id;
        $this->ownerId = $ownerId;
        $this->gitRepositoryUrl = $gitRepositoryUrl;
        $this->currentCommitHash = $currentCommitHash;

        Assertion::minCount($enabledChecks, 1, 'Review must have defined at least single automated check.');
        Assertion::allInArray($enabledChecks, AutomatedCheck::VALID_CHECK_NAMES);
        $this->enabledChecks = $enabledChecks;
    }

    public static function fromReviewDocument(ReviewDocument $document): self
    {
        return new self(
            $document->id,
            $document->userId,
            $document->gitRepositoryUrl,
            $document->currentCommitHash,
            $document->enabledChecks
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

    /**
     * @return string[]
     */
    public function getEnabledChecks(): array
    {
        return $this->enabledChecks;
    }
}
