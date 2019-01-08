<?php

declare(strict_types=1);

namespace App\Domain\Review;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Domain\Review\Event\EventInterface;
use App\Domain\User\UserInterface;
use Assert\Assertion;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @ORM\Entity()
 * @ApiFilter(SearchFilter::class, properties={"id": "exact","user.email": "exact","review.id": "exact"})
 * @ApiFilter(OrderFilter::class, properties={"createdAt": "ASC"})
 */
class ReviewEventStore
{
    /**
     * @var ReviewEventId
     * @ORM\Id()
     * @ORM\Column(type="review_event_id")
     * @Groups({"ReviewEventRead"})
     */
    private $id;

    /**
     * @var Review
     * @ORM\ManyToOne(targetEntity="Review")
     * @ORM\JoinColumn(name="review_id",referencedColumnName="id")
     * @Groups({"ReviewEventRead"})
     */
    private $review;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"ReviewEventRead"})
     */
    private $createdAt;

    /**
     * @var array
     * @ORM\Column(type="json")
     * @Groups({"ReviewEventRead"})
     */
    private $eventData;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Groups({"ReviewEventRead"})
     */
    private $eventType;

    public function __construct(ReviewEventId $id, Review $review, string $eventType, array $eventData, DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->review = $review;
        $this->eventType = $eventType;
        $this->eventData = $eventData;
        $this->createdAt = $createdAt;
    }

    /**
     * @return ReviewEventId
     */
    public function getId(): ReviewEventId
    {
        return $this->id;
    }

    /**
     * @return Review
     */
    public function getReview(): Review
    {
        return $this->review;
    }

    public function isGranted(object $user): bool
    {
        if ($user instanceof UserInterface) {
            return $this->review->isGranted($user);
        }

        return false;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return array
     */
    public function getEventData(): array
    {
        return $this->eventData;
    }

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function asEvent(DenormalizerInterface $denormalizer): EventInterface
    {
        /** @var EventInterface $obj */
        $obj = $denormalizer->denormalize($this->getEventData() + [
                'reviewId' => $this->review->getId()->toString(),
                'eventType' => $this->eventType,
            ], EventInterface::class, 'json');

        return $obj;
    }

    public static function fromEvent(EventInterface $event, Review $review, NormalizerInterface $normalizer): self
    {
        $data = $normalizer->normalize($event, 'json');
        Assertion::isArray($data);
        $type = $data['eventType'];
        unset($data['eventType'], $data['reviewId']);

        return new self(
            ReviewEventId::fromUuid4(),
            $review,
            $type,
            $data,
            new DateTimeImmutable('now')
        );
    }
}
