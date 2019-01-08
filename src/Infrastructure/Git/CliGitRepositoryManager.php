<?php

declare(strict_types=1);

namespace App\Infrastructure\Git;

use App\Application\Contract\GitRepository;
use App\Application\Contract\GitRepositoryManagerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CliGitRepositoryManager implements GitRepositoryManagerInterface
{
    /**
     * @var string
     */
    private $gitClonerPath;

    public function __construct(string $gitClonerPath)
    {
        $this->gitClonerPath = \rtrim($gitClonerPath, '/\\');
    }

    public function cloneRepository(string $repositoryUrl, string $name): GitRepository
    {
        $location = \sprintf('%s%s%s', $this->gitClonerPath, \DIRECTORY_SEPARATOR, $name);

        if (\file_exists($location)) {
            $this->removeDirectory($location);
        } else {
            $this->makeDirectory($location);
        }

        $cloneProcess = new Process(['git', 'clone', $repositoryUrl, $name], $this->gitClonerPath);
        $cloneProcess->run();
        if (!$cloneProcess->isSuccessful()) {
            throw new ProcessFailedException($cloneProcess);
        }

        return new GitRepository(
            $repositoryUrl,
            $location
        );
    }

    private function makeDirectory(string $absolutePath): void
    {
        $cloneProcess = new Process(['mkdir', '-p', $absolutePath]);
        $cloneProcess->run();
        if (!$cloneProcess->isSuccessful()) {
            throw new ProcessFailedException($cloneProcess);
        }
    }

    private function removeDirectory(string $absolutePath): void
    {
        $cloneProcess = new Process(['rm', '-rf', $absolutePath]);
        $cloneProcess->run();
        if (!$cloneProcess->isSuccessful()) {
            throw new ProcessFailedException($cloneProcess);
        }
    }

    public function removeLocalCopy(GitRepository $repository): void
    {
        $this->removeDirectory($repository->getLocation());
    }

    public function runCommand(GitRepository $repository, array $command): string
    {
        $process = new Process($command, $repository->getLocation());
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    public function checkoutCommit(GitRepository $repository, string $commitHash): void
    {
        $this->runCommand($repository, ['git', 'checkout', $commitHash]);
    }
}
