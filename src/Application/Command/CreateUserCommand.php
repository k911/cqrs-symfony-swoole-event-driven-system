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
    private $passwordHash;
    /**
     * @var array
     */
    private $roles;

    public function __construct(
        string $id,
        string $email,
        string $passwordHash,
        array $roles
    ) {
        Assertion::uuid($id);
        Assertion::email($email);
        Assertion::minCount($roles, 1, 'User must have at least one role');

        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->roles = $roles;
    }

    public static function fromUserDocument(UserDocument $document, string $passwordHash): self
    {
        return new self(
            $document->id,
            $document->email,
            $passwordHash,
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
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
