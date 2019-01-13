<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Type;

use App\Domain\Review\ReviewCommentId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Ramsey\Uuid\Doctrine\UuidType;

class ReviewCommentIdType extends UuidType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new ReviewCommentId(parent::convertToPHPValue($value, $platform));
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'review_comment_id';
    }
}
