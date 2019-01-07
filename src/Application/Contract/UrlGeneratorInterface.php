<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface UrlGeneratorInterface
{
    public function generate(string $path): string;
}
