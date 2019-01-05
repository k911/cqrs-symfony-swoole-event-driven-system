<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Projection\UserProjection;
use Ramsey\Uuid\Uuid;

final class CreateUserAction
{
    public function __invoke(UserProjection $data): UserProjection
    {
        $userId = Uuid::uuid4();
        $data->id = $userId;
        dump($userId);

        return $data;
    }
}
