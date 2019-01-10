<?php
declare(strict_types=1);

namespace App\Domain\SystemStatus;

use DateTimeImmutable;
use JsonSerializable;

final class SystemStatus implements JsonSerializable
{
    public const STATUS_OK = 'OK';
    public const STATUS_DEGRADED = 'DEGRADED';

    /**
     * @var string
     */
    private $status;

    /**
     * @var DateTimeImmutable
     */
    private $checkedAt;

    /**
     * @var iterable<ComponentStatus>
     */
    private $componentStatuses;

    /**
     * @param iterable<ComponentStatus> $componentStatues
     * @param DateTimeImmutable $checkedAt
     * @throws \Exception
     */
    public function __construct(iterable $componentStatues, DateTimeImmutable $checkedAt)
    {
        $this->componentStatuses = $componentStatues;
        $this->status = $this->computeStatus($componentStatues);
        $this->checkedAt = $checkedAt;
    }

    /**
     * @param iterable<ComponentStatus> $componentStatuses
     * @return string
     */
    private function computeStatus(iterable $componentStatuses): string
    {
        /** @var ComponentStatus $componentStatus */
        foreach ($componentStatuses as $componentStatus) {
            if (!$componentStatus->hasCheckPassed()) {
                return self::STATUS_DEGRADED;
            }
        }

        return self::STATUS_OK;
    }

    public function isOk(): bool
    {
        return $this->status === self::STATUS_OK;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCheckedAt(): DateTimeImmutable
    {
        return $this->checkedAt;
    }

    /**
     * @return iterable<ComponentStatus>
     */
    public function getComponentStatuses(): iterable
    {
        return $this->componentStatuses;
    }

    public static function fromNow(ComponentStatus ...$componentStatuses): self
    {
        /** @var iterable<ComponentStatus> $componentStatuses */
        return new self($componentStatuses, new DateTimeImmutable('now'));
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'status' => $this->getStatus(),
            'checkedAt' => $this->getCheckedAt()->format(DATE_ATOM),
            'componentStatuses' => $this->componentStatuses,
        ];
    }
}
