<?php

namespace Bermuda\App\Console;

use Symfony\Component\Console\{
    Application, Command\Command, 
    Input\InputInterface, Output\OutputInterface
};

final class SymfonyConsole implements CommandRunnerInterface
{
    private ?Application $console = null;

    /**
     * @param CommandInterface $command
     * @return SymfonyConsole
     */
    public function add(CommandInterface $command): CommandRunnerInterface
    {
        $this->getConsole()->add(
            SymfonyCommand::decorate($command)
        );
        
        return $this;
    }

    /**
     * @return Application
     */
    public function getConsole(): Application
    {
        return $this->console == null ? $this->console 
            : $this->console = new Application;
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
