<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface IdentifiedDocumentInterface
{
    /**
     * @param string $id
     *
     * @return object&IdentifiedDocumentInterface
     */
    public static function fromId(string $id);
}
