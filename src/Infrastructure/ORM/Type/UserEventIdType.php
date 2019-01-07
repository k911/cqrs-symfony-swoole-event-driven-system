<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Type;

use App\Domain\User\UserEventId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Ramsey\Uuid\Doctrine\UuidType;

class UserEventIdType extends UuidType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new UserEventId(parent::convertToPHPValue($value, $platform));
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'user_event_id';
    }
}
