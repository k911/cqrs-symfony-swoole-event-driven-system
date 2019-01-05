<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Type;

use App\Infrastructure\Uuid\RamseyUuidUserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Ramsey\Uuid\Doctrine\UuidType;

class UserIdType extends UuidType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new RamseyUuidUserId(parent::convertToPHPValue($value, $platform));
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'user_id';
    }
}
