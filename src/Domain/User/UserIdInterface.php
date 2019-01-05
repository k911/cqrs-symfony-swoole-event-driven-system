<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserIdInterface
{
    /**
     * @param object $userId
     *
     * @return bool
     */
    public function equals($userId): bool;

    public function toString(): string;
}
