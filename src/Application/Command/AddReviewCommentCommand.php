<?php

declare(strict_types=1);

namespace App\Application\Command;

final class AddReviewCommentCommand
{
    /**
     * @var string
     */
    private $reviewId;

    /**
     * @var string
     */
    private $commentId;

    /**
     * @var string
     */
    private $authorId;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @param string $reviewId
     * @param string $commentId
     * @param string $authorId
     * @param string $content
     * @param string $createdAt
     */
    public function __construct(string $reviewId, string $commentId, string $authorId, string $content, string $createdAt)
    {
        $this->reviewId = $reviewId;
        $this->commentId = $commentId;
        $this->authorId = $authorId;
        $this->content = $content;
        $this->createdAt = $createdAt;
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
    public function getCommentId(): string
    {
        return $this->commentId;
    }

    /**
     * @return string
     */
    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
