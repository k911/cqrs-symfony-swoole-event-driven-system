<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Command\CreateUserCommand;
use App\Application\Document\UserDocument;
use App\Domain\User\UserEmail;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Uuid\RamseyUuidUserId;
use Assert\Assertion;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class CreateUserAction
{
    private $commandBus;
    private $userPasswordEncoder;
    private $userRepository;

    public function __construct(
        MessageBusInterface $commandBus,
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserRepositoryInterface $userRepository
    )
    {
        $this->commandBus = $commandBus;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userRepository = $userRepository;
    }

    public function __invoke(UserDocument $data): UserDocument
    {
        Assertion::null($this->userRepository->findByEmail(new UserEmail($data->email)), 'Could not create user.');

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
