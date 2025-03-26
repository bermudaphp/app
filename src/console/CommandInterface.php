<?php

namespace Bermuda\App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CommandInterface
{
    public const SUCCESS = Command::SUCCESS;
    public const FAILURE = Command::FAILURE;

    public function getName(): string ;
    public function getDescription(): string ;
    public function executeCommand(InputInterface $input, OutputInterface $output): int ;
}
