<?php

declare(strict_types=1);

namespace App\Application\Document;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Application\Contract\IdentifiedDocumentInterface;
use App\Domain\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserChangePasswordDocument implements IdentifiedDocumentInterface
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"UserChangePasswordWrite","UserChangePasswordRead"})
     * @Assert\Uuid()
     *
     * @var string
     */
    public $id;

    /**
     * @Groups({"UserChangePasswordWrite"})
     *
     * @var string
     */
    public $oldPassword;

    /**
     * @Groups({"UserChangePasswordWrite"})
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="8",minMessage="Password must be at least 8 characters long")
     */
    public $newPassword;

    public function isGranted(object $user): bool
    {
        if ($user instanceof UserInterface) {
            return $this->id === $user->getId()->toString();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromId(string $id)
    {
        $document = new self();
        $document->id = $id;

        return $document;
    }
}
