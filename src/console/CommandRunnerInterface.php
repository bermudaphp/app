<?php

namespace Bermuda\App\Console;

use Symfony\Component\Console\{Input\InputInterface, Output\OutputInterface};

interface CommandRunnerInterface
{
    /**
     * @param CommandInterface $command
     * @return CommandRunnerInterface
     */
    public function add(CommandInterface $command): CommandRunnerInterface;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function run(InputInterface $input, OutputInterface $output): void ;
}
