<?php

declare(strict_types=1);

namespace App\Application\Cli;

use App\Application\Contract\GitRepositoryManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class TestGitCloneCommand extends Command
{
    protected static $defaultName = 'test:git:clone';
    private $gitRepositoryManager;

    public function __construct(GitRepositoryManagerInterface $gitRepositoryManager)
    {
        $this->gitRepositoryManager = $gitRepositoryManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $repo = $this->gitRepositoryManager->cloneRepository(
            'https://github.com/k911/swoole-bundle',
            'k911-swoole-bundle'
        );
        $this->gitRepositoryManager->checkoutCommit($repo, 'ef5920c3378c4ab8ace82c58e79c6000bebc1cec');
        $this->gitRepositoryManager->removeLocalCopy($repo);
    }
}
