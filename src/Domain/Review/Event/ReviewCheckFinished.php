<?php

declare(strict_types=1);

namespace App\Domain\Review\Event;

class ReviewCheckFinished implements EventInterface
{
    /**
     * @var string
     */
    private $reviewId;

    /**
     * @var string
     */
    private $commitHash;

    /**
     * @var bool
     */
    private $passed;

    /**
     * @var array
     */
    private $result;

    /**
     * @var string
     */
    private $checkName;

    /**
     * @param string $reviewId
     * @param string $commitHash
     * @param bool   $passed
     * @param array  $result
     * @param string $checkName
     */
    public function __construct(string $reviewId, string $commitHash, bool $passed, array $result, string $checkName)
    {
        $this->reviewId = $reviewId;
        $this->commitHash = $commitHash;
        $this->passed = $passed;
        $this->result = $result;
        $this->checkName = $checkName;
    }

    /**
     * @return string
     */
    public function getCommitHash(): string
    {
        return $this->commitHash;
    }

    /**
     * @return bool
     */
    public function isPassed(): bool
    {
        return $this->passed;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @return string
     */
    public function getCheckName(): string
    {
        return $this->checkName;
    }

    public function getReviewId(): string
    {
        return $this->reviewId;
    }

    public function getEventType(): string
    {
        return 'review_check_finished';
    }
}
