<?php

declare(strict_types=1);

namespace App\Application\Document;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Application\Contract\IdentifiedDocumentInterface;
use App\Domain\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserChangeEmailDocument implements IdentifiedDocumentInterface
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"UserChangeEmailWrite","UserChangeEmailRead"})
     * @Assert\Uuid()
     *
     * @var string
     */
    public $id;

    /**
     * @Groups({"UserChangeEmailWrite"})
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $password;

    /**
     * @Groups({"UserChangeEmailWrite"})
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $newEmail;

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
