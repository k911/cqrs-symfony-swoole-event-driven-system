<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Command\CreateUserCommand;
use App\Application\Document\UserDocument;
use App\Infrastructure\Uuid\RamseyUuidUserId;
use Symfony\Component\Messenger\MessageBusInterface;

final class CreateUserAction
{
    /**
     * @var MessageBusInterface
     */
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(UserDocument $data): UserDocument
    {
        if (null === $data->id) {
            $data->id = RamseyUuidUserId::fromUuid4()->toString();
        }

        $this->commandBus->dispatch(CreateUserCommand::fromUserDocument($data));

        return $data;
    }
}
