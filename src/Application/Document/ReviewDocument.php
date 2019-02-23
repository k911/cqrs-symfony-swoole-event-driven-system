<?php

declare(strict_types=1);

namespace App\Application\Document;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Application\Contract\IdentifiedDocumentInterface;
use App\Domain\Review\AutomatedCheck;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class ReviewDocument implements IdentifiedDocumentInterface
{
    /**
     * @ApiProperty(identifier=true)
     * @Groups({"ReviewRead","ReviewWrite"})
     * @Assert\Uuid()
     *
     * @var string
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Groups({"ReviewRead","ReviewWrite"})
     */
    public $gitRepositoryUrl;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Groups({"ReviewRead","ReviewWrite"})
     */
    public $currentCommitHash;

    /**
     * @Groups({"ReviewRead"})
     *
     * @var string
     */
    public $userId;

    /**
     * @var array
     * @Groups({"ReviewRead","ReviewWrite"})
     */
    public $enabledChecks;

    public static function fromId(string $id): self
    {
        $document = new self();
        $document->id = $id;

        return $document;
    }
}
