<?php

declare(strict_types=1);

namespace App\Application\Document;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Application\Contract\IdentifiedDocumentInterface;
use App\Domain\User\UserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserDocument implements SymfonyUserInterface, IdentifiedDocumentInterface
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
     * @Assert\Length(min="8",minMessage="Password must be at least 8 characters long")
     *
     * @var string|null
     */
    public $plainPassword;

    /**
     * @var string|null
     */
    public $passwordHash;

    public function isGranted(object $user): bool
    {
        if ($user instanceof UserInterface) {
            return $this->id === $user->getId()->toString();
        }

        return false;
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

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return $this->passwordHash;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}
