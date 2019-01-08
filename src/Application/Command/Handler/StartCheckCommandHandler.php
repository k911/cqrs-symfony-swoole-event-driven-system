<?php

declare(strict_types=1);

namespace App\Application\Command\Handler;

use App\Application\Command\StartCheckCommand;
use App\Application\Contract\GitRepository;
use App\Application\Contract\GitRepositoryManagerInterface;
use App\Domain\Review\AutomatedCheck;
use App\Domain\Review\Event\ReviewCheckFinished;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class StartCheckCommandHandler
{
    private $gitRepositoryManager;
    private $eventBus;

    public function __construct(GitRepositoryManagerInterface $gitRepositoryManager, MessageBusInterface $eventBus)
    {
        $this->gitRepositoryManager = $gitRepositoryManager;
        $this->eventBus = $eventBus;
    }

    private function handle(StartCheckCommand $command, GitRepository $repository): array
    {
        try {
            $this->gitRepositoryManager->checkoutCommit($repository, $command->getCommitHash());
        } catch (ProcessFailedException $exception) {
            $process = $exception->getProcess();

            return [
                false,
                [
                    'context' => 'GIT',
                    'error' => [
                        'message' => 'Could not checkout specified commit hash',
                        'code' => $process->getExitCode(),
                        'output' => $process->getOutput(),
                        'errorOutput' => $process->getErrorOutput(),
                    ],
                ],
            ];
        }

        try {
            return [
                true,
                $this->sanitizeOutput($this->runCheck($repository, $command->getCheckName())),
            ];
        } catch (ProcessFailedException $exception) {
            $process = $exception->getProcess();
            try {
                return [
                    false,
                    $this->sanitizeOutput($process->getOutput()),
                ];
            } catch (\JsonException $exception) {
                return [
                    false,
                    [
                        'context' => $command->getCheckName(),
                        'error' => [
                            'message' => 'Check failed',
                            'code' => $process->getExitCode(),
                            'output' => $process->getOutput(),
                            'errorOutput' => $process->getErrorOutput(),
                        ],
                    ],
                ];
            }
        }
    }

    public function __invoke(StartCheckCommand $command): void
    {
        $name = \bin2hex(\random_bytes(12));
        $repo = null;
        try {
            $repo = $this->gitRepositoryManager->cloneRepository($command->getGitRepositoryUrl(), $name);
            [
                $passed,
                $output,
            ] = $this->handle($command, $repo);
            $this->finishHandling($command, $passed, $output);
        } catch (ProcessFailedException $exception) {
            $process = $exception->getProcess();
            $this->finishHandling($command, false, [
                'context' => 'GIT',
                'error' => [
                    'message' => 'Could not clone repository',
                    'code' => $process->getExitCode(),
                    'output' => $process->getOutput(),
                    'errorOutput' => $process->getErrorOutput(),
                ],
            ]);
        } finally {
            $this->tryToCleanUp($repo);
        }
    }

    private function tryToCleanUp(?GitRepository $repository): void
    {
        if (null === $repository) {
            return;
        }

        try {
            $this->gitRepositoryManager->removeLocalCopy($repository);
        } catch (\Throwable $ignored) {
        }
    }

    private function finishHandling(StartCheckCommand $command, bool $passed, array $result): void
    {
        $checkFinished = new ReviewCheckFinished(
            $command->getReviewId(),
            $command->getCommitHash(),
            $passed,
            $result,
            $command->getCheckName()
        );

        $this->eventBus->dispatch($checkFinished);
    }

    private function optionallyInstallComposer(GitRepository $repository): void
    {
        if ($repository->fileExists('composer.json')) {
            $this->gitRepositoryManager->runCommand($repository, ['composer', 'install']);
        }
    }

    private function runPHPStan(GitRepository $repository): string
    {
        $bin = $repository->fileExists('vendor/bin/phpstan') ? 'vendor/bin/phpstan' : 'phpstan';

        return $this->gitRepositoryManager->runCommand($repository, [
            $bin,
            'analyse',
            '--error-format=json',
            '--no-ansi',
            '--no-interaction',
            '--no-progress',
            '--level=7',
            'src',
        ]);
    }

    private function runPHPCSFixer(GitRepository $repository): string
    {
        $bin = $repository->fileExists('vendor/bin/php-cs-fixer') ? 'vendor/bin/php-cs-fixer' : 'php-cs-fixer';

        return $this->gitRepositoryManager->runCommand($repository, [
            $bin,
            'fix',
            '--dry-run',
            '--diff',
            '--format=json',
            '--no-ansi',
            '--no-interaction',
            '--show-progress=none',
        ]);
    }

    private function runCheck(GitRepository $repository, string $checkName): string
    {
        switch ($checkName) {
            case AutomatedCheck::CHECK_NAME_PHPSTAN:
                $this->optionallyInstallComposer($repository);

                return $this->runPHPStan($repository);
                break;
            case AutomatedCheck::CHECK_NAME_PHPCSFIXER:
                $this->optionallyInstallComposer($repository);

                return $this->runPHPCSFixer($repository);
                break;
            default:
                throw new \InvalidArgumentException(\sprintf('No such check: %s', $checkName));
        }
    }

    private function sanitizeOutput(string $output): array
    {
        return \json_decode(
            \trim($output), true, 512, \JSON_THROW_ON_ERROR);
    }
}