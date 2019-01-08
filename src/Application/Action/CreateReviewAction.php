<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Command\CreateReviewCommand;
use App\Application\Document\ReviewDocument;
use App\Domain\User\User;
use App\Domain\User\UserId;
use RuntimeException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class CreateReviewAction
{
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(ReviewDocument $data, UserInterface $user): ReviewDocument
    {
        if (!$user instanceof User) {
            throw new RuntimeException('Symfony User interface must be an instance of User doctrine entity', 500);
        }

        $data->userId = $user->getId()->toString();

        if (null === $data->id) {
            $data->id = UserId::fromUuid4()->toString();
        }

        $this->commandBus->dispatch(CreateReviewCommand::fromReviewDocument($data));

        return $data;
    }
}
