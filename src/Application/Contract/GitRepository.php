<?php

declare(strict_types=1);

namespace App\Application\Contract;

final class GitRepository
{
    /**
     * @var string
     */
    private $cloneRepositoryUrl;

    /**
     * @var string
     */
    private $location;

    /**
     * @param string $cloneRepositoryUrl
     * @param string $location
     */
    public function __construct(string $cloneRepositoryUrl, string $location)
    {
        $this->cloneRepositoryUrl = $cloneRepositoryUrl;
        $this->location = \rtrim($location, '/\\');
    }

    /**
     * @return string
     */
    public function getCloneRepositoryUrl(): string
    {
        return $this->cloneRepositoryUrl;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    public function fileExists(string $filePath): bool
    {
        $filePath = \ltrim($filePath, '\\/');

        return \file_exists($filePath);
    }
}
