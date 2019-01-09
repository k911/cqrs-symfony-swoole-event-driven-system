<?php

declare(strict_types=1);

namespace App\Application\ConnectionChecker;

interface ConnectionCheckerInterface
{
    public function check(): bool;

    public function description(): string;
}
