<?php

declare(strict_types=1);

namespace App\Application\Document;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Application\Contract\IdentifiedDocumentInterface;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class ReviewAddCommentDocument implements IdentifiedDocumentInterface
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"ReviewAddCommentRead","ReviewAddCommentWrite"})
     * @Assert\Uuid()
     *
     * @var string
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=3,minMessage="Comment cannot be shorter than 3 characters")
     * @Groups({"ReviewAddCommentRead","ReviewAddCommentWrite"})
     */
    public $content;

    /**
     * @Groups({"ReviewAddCommentRead"})
     * @Assert\Uuid()
     *
     * @var string
     * @Assert\NotBlank()
     */
    public $reviewId;

    /**
     * @var string
     * @Groups({"ReviewAddCommentRead"})
     * @Assert\Uuid()
     */
    public $userId;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    public function __construct(?DateTimeImmutable $createdAt = null)
    {
        $this->createdAt = $createdAt ?? new DateTimeImmutable('now');
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt->format(DATE_ATOM);
    }

    public static function fromId(string $id): self
    {
        $document = new self();
        $document->reviewId = $id;

        return $document;
    }
}
