<?php

declare(strict_types=1);

namespace App\Application\Command;

use App\Application\Document\UserDocument;
use Assert\Assertion;

class CreateUserCommand
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $plainPassword;
    /**
     * @var array
     */
    private $roles;

    public function __construct(
        string $id,
        string $email,
        string $plainPassword,
        array $roles
    ) {
        Assertion::uuid($id);
        Assertion::email($email);
        Assertion::notBlank($plainPassword, 'Password must be provided while creating a new user');
        Assertion::minLength($plainPassword, 8, 'Password must be at least 8 characters long');
        Assertion::minCount($roles, 1, 'User must have at least one role');

        $this->id = $id;
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->roles = $roles;
    }

    public static function fromUserDocument(UserDocument $document): self
    {
        return new self(
            $document->id,
            $document->email,
            $document->plainPassword,
            $document->roles
        );
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
