<?php

declare(strict_types=1);

namespace App\Domain\User;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\ORM\Repository\UserRepository")
 */
class User implements SymfonyUserInterface, UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="user_id")
     * @Groups({"UserRead"})
     */
    private $id;

    /**
     * @var UserEmail
     *
     * @ORM\Column(type="user_email", length=180, unique=true)
     * @Groups({"UserRead"})
     */
    private $email;

    /**
     * @var array
     * @ORM\Column(type="json")
     * @Groups({"UserRead"})
     */
    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $passwordHash;

    public function __construct(UserId $userId, UserEmail $userEmail, string $passwordHash, array $roles)
    {
        $this->id = $userId;
        $this->email = $userEmail;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
    }

    public function getId(): UserIdInterface
    {
        return $this->id;
    }

    public function getEmail(): UserEmailInterface
    {
        return $this->email;
    }

    public function setEmail(UserEmail $email): void
    {
        $this->email = $email;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return $this->email->toString();
    }

    public function isAdmin(): bool
    {
        return \in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return \array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return $this->passwordHash;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
    }

    public function isGranted(object $user): bool
    {
        if ($user instanceof UserInterface) {
            return $this->id->equals($user->getId());
        }

        return false;
    }

    public static function fromEvent(Event\UserCreated $event): self
    {
        return new self(UserId::fromString($event->getUserId()),
            new UserEmail($event->getEmail()),
            $event->getPasswordHash(),
            $event->getRoles());
    }

    public function changeEmail(Event\UserEmailChanged $event): void
    {
        $this->email = new UserEmail($event->getNewEmail());
    }

    public function changePassword(Event\UserPasswordChanged $event): void
    {
        $this->passwordHash = $event->getNewPasswordHash();
    }
}
