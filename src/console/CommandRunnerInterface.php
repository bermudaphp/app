<?php

namespace Bermuda\App\Console;

use Symfony\Component\Console\{Input\InputInterface, Output\OutputInterface};

interface CommandRunnerInterface
{
    public function add(CommandInterface $command): CommandRunnerInterface;
    public function run(InputInterface $input, OutputInterface $output): void ;
}
