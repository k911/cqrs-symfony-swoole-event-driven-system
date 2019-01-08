<?php

declare(strict_types=1);

namespace App\Application\Contract;

interface GitRepositoryManagerInterface
{
    public function removeLocalCopy(GitRepository $repository): void;

    public function runCommand(GitRepository $repository, array $command): string;

    public function checkoutCommit(GitRepository $repository, string $commitHash): void;

    public function cloneRepository(string $repositoryUrl, string $name): GitRepository;
}
