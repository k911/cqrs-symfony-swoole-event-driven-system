<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Command\CreateUserCommand;
use App\Application\Document\UserChangePasswordDocument;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Uuid\RamseyUuidUserId;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $user = $this->userRepository->findById(RamseyUuidUserId::fromString($data->id));

        if (null === $user) {
            throw new NotFoundHttpException(\sprintf('User with id "%s" has not been found.', $data->id));
        }

        dump($data, $user);

//        $this->commandBus->dispatch(CreateUserCommand::fromUserDocument(
//            $data,
//            $this->userPasswordEncoder->encodePassword($data, $data->plainPassword)
//        ));

        return $data;
    }
}
