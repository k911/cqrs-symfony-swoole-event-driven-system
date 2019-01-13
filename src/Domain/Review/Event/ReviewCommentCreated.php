<?php

declare(strict_types=1);

namespace App\Domain\Review\Event;

final class ReviewCommentCreated implements EventInterface
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
     * @var string DATE_ATOM
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
        $this->authorId = $authorId;
        $this->content = $content;
        $this->commentId = $commentId;
        $this->createdAt = $createdAt;
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

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    public function getReviewId(): string
    {
        return $this->reviewId;
    }

    public function getEventType(): string
    {
        return 'review_comment_created';
    }
}
