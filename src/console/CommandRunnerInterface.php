<?php

namespace Bermuda\App\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface CommandRunnerInterface
 * @package Bermuda\App\Console
 */
interface CommandRunnerInterface
{
    /**
     * @param CommandInterface $command
     * @return $this
     */
    public function add(CommandInterface $command): self;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function run(InputInterface $input, OutputInterface $output): void ;
}
