<?php

declare(strict_types=1);

namespace App\Domain\User;

use Assert\Assertion;
use JsonSerializable;
use Serializable;

final class UserEmail implements UserEmailInterface, Serializable, JsonSerializable
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

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return $this->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        $this->email = $serialized;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->email;
    }
}
