<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Command\CreateUserCommand;
use App\Application\Document\UserDocument;
use App\Infrastructure\Uuid\RamseyUuidUserId;
use Assert\Assertion;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class CreateUserAction
{
    private $commandBus;
    private $userPasswordEncoder;

    public function __construct(MessageBusInterface $commandBus, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->commandBus = $commandBus;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function __invoke(UserDocument $data): UserDocument
    {
        if (null === $data->id) {
            $data->id = RamseyUuidUserId::fromUuid4()->toString();
        }

        Assertion::notBlank($data->plainPassword, 'Password must be provided while creating a new user');
        $this->commandBus->dispatch(CreateUserCommand::fromUserDocument(
            $data,
            $this->userPasswordEncoder->encodePassword($data, $data->plainPassword)
        ));

        return $data;
    }
}
