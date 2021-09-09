<?php

namespace Bermuda\App\Console;

use Symfony\Component\Console\{Application, Command\Command, Input\InputInterface, Output\OutputInterface};

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
        if ($this->console == null) {
            return $this->console = new Application;
        }

        return $this->console;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    public function run(InputInterface $input, OutputInterface $output): void
    {
        $this->getConsole()->run($input, $output);
    }
}
