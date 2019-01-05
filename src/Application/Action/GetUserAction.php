<?php

declare(strict_types=1);

namespace App\Application\Action;

use App\Application\Projection\UserProjection;

final class GetUserAction
{
    public function __invoke(UserProjection $data): UserProjection
    {
        dump(\func_get_args());

        return $data;
    }
}
