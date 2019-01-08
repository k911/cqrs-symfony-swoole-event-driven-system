<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Command\ChangeUserEmailCommand;
use App\Application\Document\UserChangeEmailDocument;
use App\Domain\User\UserEmail;
use App\Domain\User\UserId;
use App\Domain\User\UserRepositoryInterface;
use Assert\Assertion;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ChangeUserEmailAction
{
    private $commandBus;
    private $userPasswordEncoder;
    private $userRepository;

    public function __construct(
        MessageBusInterface $commandBus,
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserRepositoryInterface $userRepository
    ) {
        $this->commandBus = $commandBus;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userRepository = $userRepository;
    }

    public function __invoke(UserChangeEmailDocument $data): UserChangeEmailDocument
    {
        /** @var UserInterface|null $user */
        $user = $this->userRepository->findById(UserId::fromString($data->id));

        if (null === $user) {
            throw new NotFoundHttpException(\sprintf('User with id "%s" has not been found.', $data->id));
        }

        Assertion::null($this->userRepository->findByEmail(new UserEmail($data->newEmail)), 'Could not change email. Email has been already used.');

        if (!$this->userPasswordEncoder->isPasswordValid($user, $data->password)) {
            throw new BadRequestHttpException('Could not change email. Please double check your password.');
        }

        $this->commandBus->dispatch(new ChangeUserEmailCommand(
            $data->id,
            $data->newEmail
        ));

        return $data;
    }
}
