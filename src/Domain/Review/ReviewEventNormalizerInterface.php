<?php

declare(strict_types=1);

namespace App\Domain\Review;

use App\Domain\Review\Event\EventInterface;

interface ReviewEventNormalizerInterface
{
    public function normalize(EventInterface $object, string $format = null, array $context = []): array;

    public function denormalize(array $data, string $class, string $format = null, array $context = []): EventInterface;
}
