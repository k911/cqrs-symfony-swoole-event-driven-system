<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepositoryInterface
{
    public function findById(UserIdInterface $userId): ?UserInterface;

    public function findByEmail(UserEmailInterface $userEmail): ?UserInterface;
}
