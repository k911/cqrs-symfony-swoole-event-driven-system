<?php

declare(strict_types=1);

namespace App\Infrastructure\Mercure;

use App\Application\Contract\EventPublisherInterface;
use App\Application\Contract\UrlGeneratorInterface;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

final class EventPublisher implements EventPublisherInterface
{
    private $publisher;
    private $serializer;
    private $urlGenerator;

    public function __construct(
        Publisher $publisher,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->publisher = $publisher;
        $this->serializer = $serializer;
        $this->urlGenerator = $urlGenerator;
    }

    public function publish(string $topicName, string $id, object $event): void
    {
        $topicName = \rtrim($topicName, '/');
        $publisher = $this->publisher;
        $topics = [
            $this->urlGenerator->generate(\sprintf('%s/%s.json', $topicName, $id)),
            $this->urlGenerator->generate(\sprintf('%s', $topicName)),
        ];
        $publisher(new Update(
            $topics, $this->serializer->serialize($event, 'json')));
    }
}
