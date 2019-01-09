<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Event\EventInterface;
use Assert\Assertion;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @ORM\Entity()
 */
class UserEventStore
{
    /**
     * @var UserEventId
     * @ORM\Id()
     * @ORM\Column(type="user_event_id")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     */
    private $user;

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

    public function __construct(UserEventId $id, User $user, string $eventType, array $eventData, DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->user = $user;
        $this->eventType = $eventType;
        $this->eventData = $eventData;
        $this->createdAt = $createdAt;
    }

    /**
     * @return UserEventId
     */
    public function getId(): UserEventId
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function isGranted(object $user): bool
    {
        if ($user instanceof UserInterface) {
            return $this->user->isGranted($user);
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
                'userId' => $this->user->getId()->toString(),
                'eventType' => $this->eventType,
            ], EventInterface::class, 'json');

        return $obj;
    }

    public static function fromEvent(EventInterface $event, User $user, NormalizerInterface $normalizer): self
    {
        $data = $normalizer->normalize($event, 'json');
        Assertion::isArray($data);
        $type = $data['eventType'];
        unset($data['eventType'], $data['userId']);

        return new self(
            UserEventId::fromUuid4(),
            $user,
            $type,
            $data,
            new DateTimeImmutable('now')
        );
    }
}
