<?php

declare(strict_types=1);

namespace App\Domain\User;

use Assert\Assertion;

class UserEmail implements UserEmailInterface
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        Assertion::email($email);
        $this->email = $email;
    }

    public function equals(UserEmailInterface $userEmail): bool
    {
        return $this->email === $userEmail->toString();
    }

    public function toString(): string
    {
        return $this->email;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
