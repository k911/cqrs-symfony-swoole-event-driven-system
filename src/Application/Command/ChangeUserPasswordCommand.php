<?php

declare(strict_types=1);

namespace App\Application\Command;

final class ChangeUserPasswordCommand
{
    private $id;
    private $newPasswordHash;

    public function __construct(string $id, string $newPasswordHash)
    {
        $this->id = $id;
        $this->newPasswordHash = $newPasswordHash;
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
    public function getNewPasswordHash(): string
    {
        return $this->newPasswordHash;
    }
}
