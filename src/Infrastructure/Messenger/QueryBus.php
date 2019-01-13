<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger;

use App\Application\Contract\QueryBusInterface;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @see \Symfony\Component\Messenger\HandleTrait
 */
final class QueryBus implements QueryBusInterface
{
    private $queryBus;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(object $query): object
    {
        $envelope = $this->queryBus->dispatch($query);

        /** @var HandledStamp[] $handledStamps */
        $handledStamps = $envelope->all(HandledStamp::class);

        if (!$handledStamps) {
            throw new LogicException(\sprintf('Message of type "%s" was handled zero times. Exactly one handler is expected when using "%s::%s()".', \get_class($envelope->getMessage()), \get_class($this), __FUNCTION__));
        }

        if (\count($handledStamps) > 1) {
            $handlers = \implode(', ', \array_map(function (HandledStamp $stamp): string {
                return \sprintf('"%s"', $stamp->getHandlerAlias() ?? $stamp->getCallableName());
            }, $handledStamps));

            throw new LogicException(\sprintf('Message of type "%s" was handled multiple times. Only one handler is expected when using "%s::%s()", got %d: %s.', \get_class($envelope->getMessage()), \get_class($this), __FUNCTION__, \count($handledStamps), $handlers));
        }

        return $handledStamps[0]->getResult();
    }
}
