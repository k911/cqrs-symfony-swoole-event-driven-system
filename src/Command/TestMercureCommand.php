<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Contract\EventPublisherInterface;
use App\Domain\User\Event\UserCreated;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TestMercureCommand extends Command
{
    protected static $defaultName = 'test:mercure';

    private $publisher;
    private $serializer;

    public function __construct(EventPublisherInterface $publisher, SerializerInterface $serializer)
    {
        parent::__construct();
        $this->publisher = $publisher;
        $this->serializer = $serializer;
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
        $event = new UserCreated(
            '1',
            'test@test.pl',
            'hash',
            ['role', 'role2']
        );

        $this->publisher->publish('api/users/', $event->getUserId(), $event);
    }
}
