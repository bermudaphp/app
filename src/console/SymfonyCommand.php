<?php

namespace Bermuda\App\Console;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class SymfonyCommand
 * @package Bermuda\App\Console;
 */
final class SymfonyCommand extends Command
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

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->command->getName();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->command->getDescription();
    }
}
