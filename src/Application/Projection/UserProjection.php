<?php

declare(strict_types=1);

namespace App\Application\Projection;

use ApiPlatform\Core\Annotation\ApiProperty;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserProjection
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"UserRead","UserUpdate"})
     * @Assert\Uuid()
     *
     * @var UuidInterface
     */
    public $id;

    /**
     * @Groups({"UserRead","UserWrite","UserUpdate"})
     * @Assert\Email()
     *
     * @var string
     */
    public $email;

    /**
     * @Groups({"UserRead","UserWrite","UserUpdate"})
     *
     * @var string[]
     */
    public $roles;
}
