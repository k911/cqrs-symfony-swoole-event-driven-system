<?php

declare(strict_types=1);

namespace App\Domain\Review;

final class AutomatedCheck
{
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
}
