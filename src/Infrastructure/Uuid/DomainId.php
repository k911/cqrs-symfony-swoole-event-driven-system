<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use DateTime;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DomainId implements UuidInterface
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return $this->uuid->serialize();
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        $this->uuid->unserialize($serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function compareTo(UuidInterface $other): int
    {
        return $this->uuid->compareTo($other);
    }

    /**
     * {@inheritdoc}
     */
    public function equals($other): bool
    {
        return $this->uuid->equals($other);
    }

    /**
     * {@inheritdoc}
     */
    public function getBytes(): string
    {
        return $this->uuid->getBytes();
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberConverter(): NumberConverterInterface
    {
        return $this->uuid->getNumberConverter();
    }

    /**
     * {@inheritdoc}
     */
    public function getHex(): string
    {
        return $this->uuid->getHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldsHex(): array
    {
        return $this->uuid->getFieldsHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getClockSeqHiAndReservedHex(): string
    {
        return $this->uuid->getClockSeqHiAndReservedHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getClockSeqLowHex(): string
    {
        return $this->uuid->getClockSeqLowHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getClockSequenceHex(): string
    {
        return $this->uuid->getClockSequenceHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getDateTime(): DateTime
    {
        return $this->uuid->getDateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getInteger()
    {
        return $this->uuid->getInteger();
    }

    /**
     * {@inheritdoc}
     */
    public function getLeastSignificantBitsHex(): string
    {
        return $this->uuid->getLeastSignificantBitsHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getMostSignificantBitsHex(): string
    {
        return $this->uuid->getMostSignificantBitsHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeHex(): string
    {
        return $this->uuid->getNodeHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeHiAndVersionHex(): string
    {
        return $this->uuid->getTimeHiAndVersionHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeLowHex(): string
    {
        return $this->uuid->getTimeLowHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeMidHex(): string
    {
        return $this->uuid->getTimeMidHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestampHex(): string
    {
        return $this->uuid->getTimestampHex();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrn(): string
    {
        return $this->uuid->getUrn();
    }

    /**
     * {@inheritdoc}
     */
    public function getVariant(): int
    {
        return $this->uuid->getVariant();
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): ?int
    {
        return $this->uuid->getVersion();
    }

    /**
     * Converts this UUID into a string representation.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->uuid->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->uuid->jsonSerialize();
    }

    public static function fromString(string $uuid)
    {
        return new static(Uuid::fromString($uuid));
    }

    public static function fromUuid4()
    {
        return new static(Uuid::uuid4());
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
