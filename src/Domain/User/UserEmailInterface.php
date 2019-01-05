<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserEmailInterface
{
    public function equals(self $userEmail): bool;

    public function toString(): string;
}
