<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Type;

use App\Domain\Review\AutomatedCheck;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

class AutomatedChecksType extends JsonType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return \array_map(function (array $item) {
            return AutomatedCheck::fromArray($item);
        }, parent::convertToPHPValue($value, $platform));
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'automated_checks';
    }
}
