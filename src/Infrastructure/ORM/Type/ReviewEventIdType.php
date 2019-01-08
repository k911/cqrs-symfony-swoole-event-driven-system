<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Type;

use App\Domain\Review\ReviewEventId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Ramsey\Uuid\Doctrine\UuidType;

class ReviewEventIdType extends UuidType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new ReviewEventId(parent::convertToPHPValue($value, $platform));
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'review_event_id';
    }
}
