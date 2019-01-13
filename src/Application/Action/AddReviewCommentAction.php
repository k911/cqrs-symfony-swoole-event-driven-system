<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Command\AddReviewCommentCommand;
use App\Application\Document\ReviewAddCommentDocument;
use App\Domain\Review\ReviewCommentId;
use App\Domain\Review\ReviewCommentRepositoryInterface;
use App\Domain\Review\ReviewId;
use App\Domain\Review\ReviewRepositoryInterface;
use App\Domain\User\User;
use Assert\Assertion;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class AddReviewCommentAction
{
    private $commandBus;
    private $reviewRepository;
    private $reviewCommentRepository;

    public function __construct(MessageBusInterface $commandBus, ReviewRepositoryInterface $reviewRepository, ReviewCommentRepositoryInterface $reviewCommentRepository)
    {
        $this->commandBus = $commandBus;
        $this->reviewRepository = $reviewRepository;
        $this->reviewCommentRepository = $reviewCommentRepository;
    }

    public function __invoke(ReviewAddCommentDocument $data, UserInterface $user): ReviewAddCommentDocument
    {
        if (!$user instanceof User) {
            throw new RuntimeException('Symfony User interface must be an instance of User doctrine entity', 500);
        }

        $review = $this->reviewRepository->findById(ReviewId::fromString($data->reviewId));

        if (null === $review) {
            throw new NotFoundHttpException(\sprintf('Review with id "%s" has not been found.', $data->reviewId));
        }

        if (null !== $data->id) {
            Assertion::null($this->reviewCommentRepository->findById(
                ReviewCommentId::fromString($data->id)
            ), \sprintf('ReviewComment with id "%s" has already been created.', $data->id));
        } else {
            $data->id = ReviewCommentId::fromUuid4()->toString();
        }

        $data->userId = $user->getId()->toString();

        $this->commandBus->dispatch(new AddReviewCommentCommand(
            $data->reviewId,
            $data->id,
            $data->userId,
            $data->content,
            $data->getCreatedAt()
        ));

        return $data;
    }
}
