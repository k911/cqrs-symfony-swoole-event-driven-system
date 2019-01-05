<?php

declare(strict_types=1);

namespace App\Application\Projection;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Domain\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserProjection
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"UserRead","UserUpdate"})
     * @Assert\Uuid()
     *
     * @var string
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

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public static function fromId(string $id): self
    {
        $projection = new self();
        $projection->id = $id;

        return $projection;
    }

    public static function fromUser(UserInterface $source): self
    {
        $projection = new self();
        $projection->id = $source->getId()->toString();
        $projection->email = $source->getEmail()->toString();
        $projection->roles = $source->getRoles();

        return $projection;
    }
}
