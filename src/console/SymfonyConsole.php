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
    private ?Application $console = null;

    /**
     * @param CommandInterface $command
     * @return $this
     */
    public function add(CommandInterface $command): CommandRunnerInterface
    {
        $this->getConsole()->add($command instanceof Command
            ? $command : SymfonyCommand::decorate($command));
        
        return $this;
    }

    /**
     * @return Application
     */
    public function getConsole(): Application
    {
        if ($this->console == null)
        {
            return $this->console = new Application;
        }

        return $this->console;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function run(InputInterface $input, OutputInterface $output): void
    {
        $this->getConsole()->run($input, $output);
    }
}
