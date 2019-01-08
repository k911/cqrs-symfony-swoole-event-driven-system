<?php

declare(strict_types=1);

namespace App\Application\Document;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Application\Contract\IdentifiedDocumentInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class ReviewChangeCommitDocument implements IdentifiedDocumentInterface
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"ReviewChangeCommitRead","ReviewChangeCommitWrite"})
     * @Assert\Uuid()
     *
     * @var string
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Groups({"ReviewChangeCommitRead","ReviewChangeCommitWrite"})
     */
    public $newCommitHash;

    /**
     * @Groups({"ReviewChangeCommitRead"})
     *
     * @var string
     */
    public $userId;

    public static function fromId(string $id): self
    {
        $document = new self();
        $document->id = $id;

        return $document;
    }
}
