<?php

namespace Bermuda\App\Console;


use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class SymfonyConsole
 * @package Bermuda\App\Console
 */
final class SymfonyConsole implements CommandRunnerInterface
{
    /**
     * @var Command[]
     */
    private array $commands = [];

    /**
     * @param CommandInterface $command
     * @return $this
     */
    public function add(CommandInterface $command): CommandRunnerInterface
    {
        if (!$command instanceof Command)
        {
            $command = new class($command) extends Command
            {
                private CommandInterface $command;

                public function __construct(CommandInterface $command)
                {
                    $this->command = $command;
                    parent::__construct(null);
                }

                protected function execute(InputInterface $input, OutputInterface $output)
                {
                    return ($this->command)($input, $output);
                }

                public function getName()
                {
                    return $this->command->getName();
                }

                public function getDescription()
                {
                    return $this->command->getDescription();
                }
            };
        }

        $this->commands[] = $command;
        return $this;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function run(InputInterface $input, OutputInterface $output): void
    {
        ($console = new Application())
            ->addCommands($this->commands);

        $console->run($input, $output);
    }
}
