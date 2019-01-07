<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Infrastructure\Uuid\DomainId;

final class UserId extends DomainId implements UserIdInterface
{
}
