<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Projection\UserProjection;
use App\Infrastructure\Uuid\RamseyUuidUserId;

final class CreateUserAction
{
    public function __invoke(UserProjection $data): UserProjection
    {
        $data->id = RamseyUuidUserId::fromUuid4()->toString();

        dump($data);

        return $data;
    }
}
