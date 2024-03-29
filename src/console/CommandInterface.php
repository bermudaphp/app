<?php

namespace Bermuda\App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CommandInterface
{
    public const success = Command::SUCCESS;
    public const failure = Command::FAILURE;

    public function getName(): string ;
    public function getDescription(): string ;
    public function __invoke(InputInterface $input, OutputInterface $output): int ;
}
