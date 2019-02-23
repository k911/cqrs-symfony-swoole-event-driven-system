<?php

declare(strict_types=1);

namespace App\Domain\Review;

use App\Domain\Review\Event\EventInterface;
use App\Domain\User\User;
use App\Domain\User\UserInterface;
use Assert\Assertion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\ORM\Repository\ReviewRepository")
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
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $gitRepositoryUrl;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $currentCommitHash;

    /**
     * @var string[]
     * @ORM\Column(type="json")
     */
    private $enabledChecks;

    /**
     * @var AutomatedCheck[]
     * @ORM\Column(type="automated_checks")
     */
    private $automatedChecks;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $automatedChecksStatus;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Domain\User\User")
     * @ORM\JoinColumn(name="owner_id",referencedColumnName="id")
     */
    private $owner;

    /**
     * @var iterable<ReviewComment>&ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Domain\Review\ReviewComment",mappedBy="review", cascade={"persist","remove"})
     */
    private $comments;

    /**
     * @param ReviewId $id
     * @param string   $gitRepositoryUrl
     * @param string   $currentCommitHash
     * @param User     $owner
     * @param string[] $enabledChecks
     */
    public function __construct(ReviewId $id, string $gitRepositoryUrl, string $currentCommitHash, User $owner, array $enabledChecks)
    {
        $this->id = $id;
        $this->owner = $owner;

        Assertion::notBlank($gitRepositoryUrl);
        $this->gitRepositoryUrl = $gitRepositoryUrl;

        Assertion::notBlank($currentCommitHash);
        $this->currentCommitHash = $currentCommitHash;

        $this->changeStatus(self::STATUS_OPEN);
        $this->changeAutomatedChecksStatus(self::AUTOMATED_CHECKS_STATUS_WAITING);
        $this->automatedChecks = [];
        $this->comments = new ArrayCollection();

        $this->enabledChecks = [];
        foreach ($enabledChecks as $enabledCheck) {
            $this->enableCheck($enabledCheck);
        }

        Assertion::greaterThan(\count($this->enabledChecks), 0, \sprintf('At least a single automated check must be enabled. Checks: %s', \implode(', ', AutomatedCheck::VALID_CHECK_NAMES)));
    }

    public function addComment(Event\ReviewCommentCreated $commentCreated, User $commentAuthor): void
    {
        $this->comments->add(
            ReviewComment::fromEvent($commentCreated, $this, $commentAuthor)
        );
    }

    public function apply(EventInterface $event, ...$args): void
    {
        $eventClass = \get_class($event);
        switch ($eventClass) {
            case Event\ReviewCommentCreated::class:
                /** @var Event\ReviewCommentCreated $reviewCommentCreated */
                $reviewCommentCreated = $event;
                Assertion::isInstanceOf($args[0], User::class);
                $this->addComment($reviewCommentCreated, $args[0]);
                break;
            case Event\ReviewCommitChanged::class:
                /** @var Event\ReviewCommitChanged $reviewCommitChanged */
                $reviewCommitChanged = $event;
                $this->currentCommitHash = $reviewCommitChanged->getNewCommitHash();
                break;
            case Event\ReviewCheckFinished::class:
                /** @var Event\ReviewCheckFinished $reviewCheckFinished */
                $reviewCheckFinished = $event;
                $this->addAutomatedCheck(new AutomatedCheck(
                    $reviewCheckFinished->getCheckName(),
                    $reviewCheckFinished->getCommitHash(),
                    $reviewCheckFinished->getResult(),
                    $reviewCheckFinished->isPassed()
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

    private function enableCheck(string $checkName): void
    {
        Assertion::inArray($checkName, AutomatedCheck::VALID_CHECK_NAMES);
        $this->enabledChecks[] = $checkName;
    }

    private function addAutomatedCheck(AutomatedCheck $automatedCheck): void
    {
        $this->automatedChecks[] = $automatedCheck;
        $this->reviewAutomatedCheckStatus();
    }

    private function reviewAutomatedCheckStatus(): void
    {
        $needed = \array_flip($this->getEnabledChecks());
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

    public static function fromEvent(Event\ReviewCreated $event, User $owner): self
    {
        return new self(ReviewId::fromString($event->getReviewId()),
            $event->getGitRepositoryUrl(),
            $event->getCommitHash(),
            $owner,
            $event->getEnabledChecks()
        );
    }

    public function isGranted(object $user): bool
    {
        if ($user instanceof UserInterface) {
            return $this->owner->isGranted($user);
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
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @return iterable<ReviewComment>
     */
    public function getComments(): iterable
    {
        return $this->comments;
    }

    /**
     * @return string[]
     */
    public function getEnabledChecks(): array
    {
        return $this->enabledChecks;
    }
}
