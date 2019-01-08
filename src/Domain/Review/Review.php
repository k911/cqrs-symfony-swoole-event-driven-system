<?php

declare(strict_types=1);

namespace App\Domain\Review;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Domain\Review\Event\EventInterface;
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

    public const AUTOMATED_CHECKS_STATUS_FAILED = 'FAILED';
    public const AUTOMATED_CHECKS_STATUS_SUCCEED = 'SUCCEED';
    public const AUTOMATED_CHECKS_STATUS_WAITING = 'WAITING';
    public const VALID_AUTOMATED_CHECKS_STATUS = [
        self::AUTOMATED_CHECKS_STATUS_FAILED,
        self::AUTOMATED_CHECKS_STATUS_SUCCEED,
        self::AUTOMATED_CHECKS_STATUS_WAITING,
    ];

    /**
     * @var ReviewId
     * @ORM\Id()
     * @ORM\Column(type="review_id")
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
     * @ORM\Column(type="automated_checks")
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
     * @var string
     * @ORM\Column(type="string")
     * @Groups({"ReviewRead"})
     */
    private $automatedChecksStatus;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Domain\User\User")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     * @Groups({"ReviewRead"})
     */
    private $user;

    /**
     * @param ReviewId $id
     * @param string   $gitRepositoryUrl
     * @param string   $currentCommitHash
     * @param User     $user
     */
    public function __construct(ReviewId $id, string $gitRepositoryUrl, string $currentCommitHash, User $user)
    {
        $this->id = $id;
        $this->user = $user;

        Assertion::notBlank($gitRepositoryUrl);
        $this->gitRepositoryUrl = $gitRepositoryUrl;

        Assertion::notBlank($currentCommitHash);
        $this->currentCommitHash = $currentCommitHash;

        $this->changeStatus(self::STATUS_OPEN);
        $this->changeAutomatedChecksStatus(self::AUTOMATED_CHECKS_STATUS_WAITING);
        $this->automatedChecks = [];
    }

    public function apply(EventInterface $event): void
    {
        $eventClass = \get_class($event);
        switch ($eventClass) {
            case Event\ReviewCheckFinished::class:
                /* @var Event\ReviewCheckFinished $event */
                $this->addAutomatedCheck(new AutomatedCheck(
                    $event->getCheckName(),
                    $event->getCommitHash(),
                    $event->getResult(),
                    $event->isPassed()
                ));
                break;
            case Event\ReviewNeedsCheck::class:
                $this->automatedChecks = [];
                $this->changeAutomatedChecksStatus(self::AUTOMATED_CHECKS_STATUS_WAITING);
                break;
            default:
                throw new \InvalidArgumentException(\sprintf('Review Event "%s" has no handler.', $eventClass));
        }
    }

    private function requiredChecksToPass(): array
    {
        return [
            AutomatedCheck::CHECK_NAME_PHPCSFIXER,
            AutomatedCheck::CHECK_NAME_PHPSTAN,
        ];
    }

    private function addAutomatedCheck(AutomatedCheck $automatedCheck): void
    {
        $this->automatedChecks[] = $automatedCheck;
        $this->reviewAutomatedCheckStatus();
    }

    private function reviewAutomatedCheckStatus(): void
    {
        $needed = \array_flip($this->requiredChecksToPass());
        $passedCount = 0;

        foreach ($this->automatedChecks as $automatedCheck) {
            if (!$automatedCheck->hasSucceeded()) {
                $this->changeAutomatedChecksStatus(self::AUTOMATED_CHECKS_STATUS_FAILED);

                return;
            }

            if (\array_key_exists($automatedCheck->getName(), $needed)) {
                ++$passedCount;
            }
        }

        $succeeded = \count($needed) === $passedCount;
        $this->changeAutomatedChecksStatus($succeeded ? self::AUTOMATED_CHECKS_STATUS_SUCCEED : self::AUTOMATED_CHECKS_STATUS_WAITING);
    }

    public static function fromEvent(Event\ReviewCreated $event, User $user): self
    {
        return new self(ReviewId::fromString($event->getReviewId()),
            $event->getGitRepositoryUrl(),
            $event->getCommitHash(),
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

    private function changeAutomatedChecksStatus(string $status): void
    {
        Assertion::inArray($status, self::VALID_AUTOMATED_CHECKS_STATUS);
        $this->automatedChecksStatus = $status;
    }

    private function changeStatus(string $status): void
    {
        Assertion::inArray($status, self::VALID_STATUSES);
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getAutomatedChecksStatus(): string
    {
        return $this->automatedChecksStatus;
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
