<?php

declare(strict_types=1);

namespace App\Infrastructure\ORM;

use App\Application\Contract\ConnectionCheckerInterface;
use Doctrine\ORM\EntityManagerInterface;

final class EntityManagerConnectionChecker implements ConnectionCheckerInterface
{
    private $connection;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->connection = $entityManager->getConnection();
    }

    public function check(): bool
    {
        return $this->connection->ping();
    }

    public function description(): string
    {
        return 'SQL Database';
    }
}
