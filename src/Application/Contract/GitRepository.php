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

    public function isExecutable(string $executablePath): bool
    {
        return \is_executable($this->absolutePathFromRelative($executablePath));
    }

    public function fileExists(string $filePath): bool
    {
        return \file_exists($this->absolutePathFromRelative($filePath));
    }

    private function absolutePathFromRelative($path): string
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' === $part) {
                continue;
            }
            if ('..' === $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return sprintf('%s/%s', $this->location, implode(DIRECTORY_SEPARATOR, $absolutes));
    }
}
