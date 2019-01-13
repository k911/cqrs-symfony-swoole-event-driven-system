<?php

declare(strict_types=1);

namespace App\Domain\SystemStatus;

use JsonSerializable;

final class ComponentStatus implements JsonSerializable
{
    /**
     * @var bool
     */
    private $checkPassed;

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     * @param bool   $passed
     */
    public function __construct(string $name, bool $passed)
    {
        $this->name = $name;
        $this->checkPassed = $passed;
    }

    /**
     * @return bool
     */
    public function hasCheckPassed(): bool
    {
        return $this->checkPassed;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'passedCheck' => $this->hasCheckPassed(),
        ];
    }
}
