<?php

declare(strict_types=1);

namespace App\Application\Document;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Domain\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserDocument
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
     * @Groups({"UserWrite","UserUpdate"})
     *
     * @var string
     */
    public $plainPassword;

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public static function fromId(string $id): self
    {
        $document = new self();
        $document->id = $id;

        return $document;
    }

    public static function fromUser(UserInterface $source): self
    {
        $document = new self();
        $document->id = $source->getId()->toString();
        $document->email = $source->getEmail()->toString();
        $document->roles = $source->getRoles();

        return $document;
    }
}
