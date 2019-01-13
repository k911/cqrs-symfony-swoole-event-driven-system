<?php

declare(strict_types=1);

namespace App\Application\Query;

final class QueryStatus
{
    /**
     * @var bool
     */
    private $localChecksOnly;

    /**
     * @param bool $localChecksOnly
     */
    public function __construct(bool $localChecksOnly)
    {
        $this->localChecksOnly = $localChecksOnly;
    }

    public function doLocalChecksOnly(): bool
    {
        return $this->localChecksOnly;
    }
}
