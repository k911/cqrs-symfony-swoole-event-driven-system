<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Document\UserDocument;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Uuid\RamseyUuidUserId;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetUserAction
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(UserDocument $data): UserDocument
    {
        $user = $this->userRepository->findById(RamseyUuidUserId::fromString($data->id));
        if (null === $user) {
            throw new NotFoundHttpException(\sprintf('User with id "%s" has not been found.', $data->id));
        }

        return UserDocument::fromUser($user);
    }
}
