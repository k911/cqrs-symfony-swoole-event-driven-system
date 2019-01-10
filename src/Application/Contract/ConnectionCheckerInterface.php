<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface ConnectionCheckerInterface
{
    public function check(): bool;

    public function description(): string;
}
