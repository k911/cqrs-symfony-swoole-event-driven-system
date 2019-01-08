<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Command\ChangeReviewCommitCommand;
use App\Application\Document\ReviewChangeCommitDocument;
use App\Domain\Review\ReviewId;
use App\Domain\Review\ReviewRepositoryInterface;
use App\Domain\User\User;
use Assert\Assertion;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

final class ChangeReviewCommitAction
{
    private $commandBus;
    private $reviewRepository;

    public function __construct(MessageBusInterface $commandBus, ReviewRepositoryInterface $reviewRepository)
    {
        $this->commandBus = $commandBus;
        $this->reviewRepository = $reviewRepository;
    }

    public function __invoke(ReviewChangeCommitDocument $data, UserInterface $user): ReviewChangeCommitDocument
    {
        $review = $this->reviewRepository->findById(ReviewId::fromString($data->id));

        if (null === $review) {
            throw new NotFoundHttpException(\sprintf('Review with id "%s" has not been found.', $data->id));
        }

        Assertion::notEq($data->newCommitHash, $review->getCurrentCommitHash(), 'Commit has has not changed');

        if (!$user instanceof User) {
            throw new RuntimeException('Symfony User interface must be an instance of User doctrine entity', 500);
        }

        if (!$review->isGranted($user) && !$user->isAdmin()) {
            throw new AccessDeniedException();
        }

        $data->userId = $user->getId()->toString();

        $this->commandBus->dispatch(ChangeReviewCommitCommand::fromDocument($data));

        return $data;
    }
}
