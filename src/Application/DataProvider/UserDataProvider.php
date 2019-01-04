<?php
declare(strict_types=1);

namespace App\Application\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Application\Projection\UserProjection;
use Ramsey\Uuid\Uuid;

final class UserDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return UserProjection::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?UserProjection
    {
        $userProjection = new UserProjection();
        $userProjection->id = Uuid::fromString($id);
        return $userProjection;
    }
}
