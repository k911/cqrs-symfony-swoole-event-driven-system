<?php

declare(strict_types=1);

namespace App\Domain\User;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\ORM\Repository\UserRepository")
 * @ApiFilter(SearchFilter::class, properties={"id": "exact","email": "exact"})
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

    public static function fromEvent(UserId $userId, Event\UserCreated $event): self
    {
        return new self($userId,
            new UserEmail($event->getEmail()),
            $event->getPasswordHash(),
            $event->getRoles());
    }

    public function applyChangePassword(Event\UserPasswordChanged $event): void
    {
        $this->passwordHash = $event->getNewPasswordHash();
    }
}
