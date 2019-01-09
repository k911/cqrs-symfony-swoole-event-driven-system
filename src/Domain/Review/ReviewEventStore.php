<?php

declare(strict_types=1);

namespace App\Domain\Review;

use App\Domain\Review\Event\EventInterface;
use App\Domain\User\UserInterface;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ReviewEventStore
{
    /**
     * @var ReviewEventId
     * @ORM\Id()
     * @ORM\Column(type="review_event_id")
     */
    private $id;

    /**
     * @var Review
     * @ORM\ManyToOne(targetEntity="Review")
     * @ORM\JoinColumn(name="review_id",referencedColumnName="id")
     */
    private $review;

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private $eventData;

    /**
     * @var string
     * @ORM\Column(type="string")
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

    public function asEvent(ReviewEventNormalizerInterface $eventNormalizer): EventInterface
    {
        return $eventNormalizer->denormalize($this->getEventData() + [
                'reviewId' => $this->review->getId()->toString(),
                'eventType' => $this->eventType,
            ], EventInterface::class, 'json');
    }

    public static function fromEvent(EventInterface $event, Review $review, ReviewEventNormalizerInterface $eventNormalizer): self
    {
        $data = $eventNormalizer->normalize($event, 'json');
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
