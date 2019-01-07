<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface EventPublisherInterface
{
    public function publish(string $topicName, string $id, object $event): void;
}
