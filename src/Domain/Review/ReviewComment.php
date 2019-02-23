<?php

declare(strict_types=1);

namespace App\Domain\Review;

use App\Domain\User\User;
use App\Domain\User\UserInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\ORM\Repository\ReviewCommentRepository"))
 */
class ReviewComment
{
    /**
     * @var ReviewCommentId
     * @ORM\Id()
     * @ORM\Column(type="review_comment_id")
     */
    private $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $outdated;

    /**
     * @var Review
     * @ORM\ManyToOne(targetEntity="Review",inversedBy="comments")
     * @ORM\JoinColumn(name="review_id",referencedColumnName="id")
     */
    private $review;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Domain\User\User")
     * @ORM\JoinColumn(name="author_id",referencedColumnName="id")
     */
    private $author;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @param ReviewCommentId   $id
     * @param Review            $review
     * @param User              $author
     * @param string            $content
     * @param DateTimeImmutable $createdAt
     * @param bool              $outdated
     *
     * @throws \Exception
     */
    public function __construct(ReviewCommentId $id, Review $review, User $author, string $content, DateTimeImmutable $createdAt, bool $outdated)
    {
        $this->id = $id;
        $this->review = $review;
        $this->author = $author;
        $this->content = $content;
        $this->outdated = $outdated;
        $this->createdAt = $createdAt;
        $this->updatedAt = $createdAt;
    }

    public function isGranted(object $user): bool
    {
        if ($user instanceof UserInterface) {
            return $this->author->isGranted($user);
        }

        return false;
    }

    public static function fromEvent(Event\ReviewCommentCreated $event, Review $review, User $author): self
    {
        /** @var DateTimeImmutable $createdAt */
        $createdAt = DateTimeImmutable::createFromFormat(DATE_ATOM, $event->getCreatedAt());

        return new self(
            ReviewCommentId::fromString($event->getCommentId()),
            $review,
            $author,
            $event->getContent(),
            $createdAt,
            false
        );
    }

    /**
     * @return ReviewCommentId
     */
    public function getId(): ReviewCommentId
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isOutdated(): bool
    {
        return $this->outdated;
    }

    /**
     * @return Review
     */
    public function getReview(): Review
    {
        return $this->review;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
