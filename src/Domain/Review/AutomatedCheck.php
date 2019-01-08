<?php

declare(strict_types=1);

namespace App\Domain\Review;

use Assert\Assertion;

final class AutomatedCheck
{
    public const CHECK_NAME_PHPSTAN = 'PHPStan';
    public const CHECK_NAME_PHPCSFIXER = 'PHP-CS-Fixer';

    public const VALID_CHECK_NAMES = [
        self::CHECK_NAME_PHPCSFIXER,
        self::CHECK_NAME_PHPSTAN,
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $commitHash;

    /**
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    private $passed;

    /**
     * @param string $name
     * @param string $commitHash
     * @param array  $data
     * @param bool   $passed
     */
    public function __construct(string $name, string $commitHash, array $data, bool $passed)
    {
        Assertion::inArray($name, self::VALID_CHECK_NAMES);
        $this->name = $name;
        $this->commitHash = $commitHash;
        $this->data = $data;
        $this->passed = $passed;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCommitHash(): string
    {
        return $this->commitHash;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isPassed(): bool
    {
        return $this->passed;
    }
}
