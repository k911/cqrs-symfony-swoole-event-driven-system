<?php

declare(strict_types=1);

namespace App\Application\Query\Handler;

use App\Application\Query\QueryStatus;
use App\Domain\SystemStatus\ComponentStatus;
use App\Domain\SystemStatus\SystemStatus;
use App\Infrastructure\ORM\EntityManagerConnectionChecker;
use App\Infrastructure\Predis\RedisConnectionChecker;

final class QueryStatusHandler
{
    private $entityManagerConnectionChecker;
    private $redisConnectionChecker;

    public function __construct(
        EntityManagerConnectionChecker $entityManagerConnectionChecker,
        RedisConnectionChecker $redisConnectionChecker
    ) {
        $this->entityManagerConnectionChecker = $entityManagerConnectionChecker;
        $this->redisConnectionChecker = $redisConnectionChecker;
    }

    public function __invoke(QueryStatus $query): SystemStatus
    {
        if ($query->doLocalChecksOnly()) {
            return SystemStatus::fromNow();
        }

        return SystemStatus::fromNow(
            new ComponentStatus('Redis Cache', $this->redisConnectionChecker->check()),
            new ComponentStatus('DBMS', $this->entityManagerConnectionChecker->check()),
            );
    }
}
