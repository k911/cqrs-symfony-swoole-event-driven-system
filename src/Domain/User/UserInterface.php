<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserInterface
{
    /**
     * @return UserIdInterface
     */
    public function getId(): UserIdInterface;

    /**
     * @return UserEmailInterface
     */
    public function getEmail(): UserEmailInterface;

    /**
     * @return string[]
     */
    public function getRoles(): array;
}
