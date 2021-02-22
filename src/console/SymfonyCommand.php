<?php

namespace Bermuda\App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SymfonyCommand
 * @package Bermuda\App\Console;
 */
class SymfonyCommand extends Command implements CommandInterface
{
    public static function decorate(CommandInterface $command): self
    {
        return new class($command) extends SymfonyCommand
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
            public function getName(): string
            {
                return $this->command->getName();
            }

            /**
             * @return string
             */
            public function getDescription(): string
            {
                return $this->command->getDescription();
            }
        };
    }
}
