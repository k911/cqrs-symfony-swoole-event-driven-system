<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Command\ChangeUserPasswordCommand;
use App\Application\Document\UserChangePasswordDocument;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Uuid\RamseyUuidUserId;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ChangeUserPasswordAction
{
    private $commandBus;
    private $userPasswordEncoder;
    /**
     * @var UserRepositoryInterface
     */
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

    public function __invoke(UserChangePasswordDocument $data): UserChangePasswordDocument
    {
        /** @var UserInterface|null $user */
        $user = $this->userRepository->findById(RamseyUuidUserId::fromString($data->id));

        if (null === $user) {
            throw new NotFoundHttpException(\sprintf('User with id "%s" has not been found.', $data->id));
        }

        if (!$this->userPasswordEncoder->isPasswordValid($user, $data->oldPassword)) {
            throw new BadRequestHttpException('Could not change password. Please double check your old password.');
        }

        $this->commandBus->dispatch(new ChangeUserPasswordCommand(
            $data->id,
            $this->userPasswordEncoder->encodePassword($user, $data->newPassword)
        ));

        return $data;
    }
}
