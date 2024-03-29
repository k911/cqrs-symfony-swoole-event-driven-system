<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Type;

use App\Domain\User\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Ramsey\Uuid\Doctrine\UuidType;

class UserIdType extends UuidType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new UserId(parent::convertToPHPValue($value, $platform));
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'user_id';
    }
}
