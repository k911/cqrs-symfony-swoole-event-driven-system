<?php

declare(strict_types=1);

namespace App\Infrastructure\Predis;

use App\Application\Contract\ConnectionCheckerInterface;
use Predis\Client;

final class RedisConnectionChecker implements ConnectionCheckerInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function check(): bool
    {
        try {
            $this->client->ping();
        } catch (\Throwable $exception) {
            return false;
        }

        return true;
    }

    public function description(): string
    {
        return 'Redis Cache';
    }
}
