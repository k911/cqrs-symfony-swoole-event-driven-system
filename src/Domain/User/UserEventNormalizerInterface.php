<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Event\EventInterface;

interface UserEventNormalizerInterface
{
    public function normalize(EventInterface $object, string $format = null, array $context = []): array;

    public function denormalize(array $data, string $class, string $format = null, array $context = []): EventInterface;
}
