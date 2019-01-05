<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM\Type;

use App\Domain\User\UserEmail;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class UserEmailType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (string) $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new UserEmail($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'user_email';
    }
}
