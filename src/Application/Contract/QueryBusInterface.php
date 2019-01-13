<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface QueryBusInterface
{
    /**
     * Synchronously handles a query to provide result.
     *
     * @param object $query
     *
     * @return object result of handled query
     */
    public function handle(object $query): object;
}
