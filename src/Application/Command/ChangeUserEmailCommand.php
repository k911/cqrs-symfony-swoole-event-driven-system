<?php

declare(strict_types=1);

namespace App\Application\Command;

final class ChangeUserEmailCommand
{
    private $id;
    private $newEmail;

    public function __construct(string $id, string $newEmail)
    {
        $this->id = $id;
        $this->newEmail = $newEmail;
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
    public function getNewEmail(): string
    {
        return $this->newEmail;
    }
}
