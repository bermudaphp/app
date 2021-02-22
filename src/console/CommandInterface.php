<?php

namespace Bermuda\App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface CommandInterface
 * @package Bermuda\App\Console
 */
interface CommandInterface
{
    public const success = Command::SUCCESS;
    public const failure = Command::FAILURE;

    public function getName(): string ;
    public function getDescription(): string;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function __invoke(InputInterface $input, OutputInterface $output): int ;
}
