<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\CreateUserCommand;
use App\Domain\User\User;
use App\Domain\User\UserEmail;
use App\Infrastructure\Uuid\RamseyUuidUserId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function __invoke(CreateUserCommand $command): void
    {
        dump($command);

        $user = new User(
            RamseyUuidUserId::fromString($command->getId()),
            new UserEmail($command->getEmail())
        );
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user, $command->getPlainPassword()
        ));
        $user->setRoles($command->getRoles());

        $this->entityManager->persist($user);
    }
}
