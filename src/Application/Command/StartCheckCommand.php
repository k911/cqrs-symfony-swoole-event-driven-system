<?php

declare(strict_types=1);

namespace App\Application\Command;

class StartCheckCommand
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
    /**
     * @var string
     */
    private $checkName;

    public function __construct(string $reviewId, string $checkName, string $gitRepositoryUrl, string $commitHash)
    {
        $this->reviewId = $reviewId;
        $this->gitRepositoryUrl = $gitRepositoryUrl;
        $this->commitHash = $commitHash;
        $this->checkName = $checkName;
    }

    public function getCheckName(): string
    {
        return $this->checkName;
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
}
