<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Type;

use App\Domain\Review\ReviewId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Ramsey\Uuid\Doctrine\UuidType;

class ReviewIdType extends UuidType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new ReviewId(parent::convertToPHPValue($value, $platform));
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'review_id';
    }
}
