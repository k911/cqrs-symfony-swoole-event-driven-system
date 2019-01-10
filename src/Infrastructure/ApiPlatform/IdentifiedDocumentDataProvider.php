<?php

declare(strict_types=1);

namespace App\Infrastructure\ApiPlatform;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Application\Contract\IdentifiedDocumentInterface;

final class IdentifiedDocumentDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return \is_subclass_of($resourceClass, IdentifiedDocumentInterface::class);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?IdentifiedDocumentInterface
    {
        return $resourceClass::fromId($id);
    }
}
