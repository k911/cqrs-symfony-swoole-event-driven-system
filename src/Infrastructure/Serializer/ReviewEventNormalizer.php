<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer;

use App\Domain\Review\Event\EventInterface;
use App\Domain\Review\ReviewEventNormalizerInterface;
use Assert\Assertion;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ReviewEventNormalizer implements ReviewEventNormalizerInterface
{
    private $normalizer;
    private $denormalizer;

    public function __construct(NormalizerInterface $normalizer, DenormalizerInterface $denormalizer)
    {
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
    }

    public function normalize(EventInterface $object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);
        Assertion::isArray($data);

        return $data;
    }

    public function denormalize(array $data, string $class, string $format = null, array $context = []): EventInterface
    {
        $obj = $this->denormalizer->denormalize($data, $class, $format, $context);
        Assertion::isInstanceOf($obj, EventInterface::class);
        /* @var $obj EventInterface */
        return $obj;
    }
}
