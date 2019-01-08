<?php

declare(strict_types=1);

namespace App\Domain\Review;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Domain\User\User;
use App\Domain\User\UserInterface;
use Assert\Assertion;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ApiFilter(SearchFilter::class, properties={"id": "exact","user.email": "exact","user.id": "exact"})
 */
class Review
{
    public const STATUS_OPEN = 'OPEN';
    public const STATUS_CLOSED = 'CLOSED';
    public const VALID_STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_CLOSED,
    ];

    /**
     * @var ReviewId
     * @ORM\Id()
     * @ORM\Column(type="user_id")
     * @Groups({"ReviewRead"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Groups({"ReviewRead"})
     */
    private $gitRepositoryUrl;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Groups({"ReviewRead"})
     */
    private $currentCommitHash;

    /**
     * @var AutomatedCheck[]
     * @ORM\Column(type="json_array")
     * @Groups({"ReviewRead"})
     */
    private $automatedChecks;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Groups({"ReviewRead"})
     */
    private $status;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Domain\User\User")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     * @Groups({"ReviewRead"})
     */
    private $user;

    /**
     * @param ReviewId $id
     * @param string $gitRepositoryUrl
     * @param string $currentCommitHash
     * @param User $user
     */
    public function __construct(ReviewId $id, string $gitRepositoryUrl, string $currentCommitHash, User $user)
    {
        $this->id = $id;

        Assertion::notBlank($gitRepositoryUrl);
        $this->gitRepositoryUrl = $gitRepositoryUrl;

        Assertion::notBlank($currentCommitHash);
        $this->currentCommitHash = $currentCommitHash;
        $this->user = $user;

        $this->status = self::STATUS_OPEN;
        $this->automatedChecks = [];
    }

    public static function fromEvent(Event\ReviewCreated $event, User $user): self
    {
        return new self(ReviewId::fromString($event->getReviewId()),
            $event->getGitRepositoryUrl(),
            $event->getCurrentCommitHash(),
            $user
        );
    }

    public function isGranted(object $user): bool
    {
        if ($user instanceof UserInterface) {
            return $this->user->isGranted($user);
        }

        return false;
    }

    /**
     * @return ReviewId
     */
    public function getId(): ReviewId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getGitRepositoryUrl(): string
    {
        return $this->gitRepositoryUrl;
    }

    /**
     * @return string
     */
    public function getCurrentCommitHash(): string
    {
        return $this->currentCommitHash;
    }

    /**
     * @return AutomatedCheck[]
     */
    public function getAutomatedChecks(): array
    {
        return $this->automatedChecks;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
