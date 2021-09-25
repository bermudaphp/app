<?php

namespace Bermuda\App\Console;

use Symfony\Component\Console\{
    Command\Command, 
    Input\InputInterface, 
    Output\OutputInterface
};

abstract class SymfonyCommand extends Command implements CommandInterface
{
    public static function decorate(CommandInterface $command): self
    {
        return new class($command) extends SymfonyCommand {
            public function __construct(private CommandInterface $command)
            {
                parent::__construct(null);
            }

            /**
             * @inerhitDoc 
             */
            protected function execute(InputInterface $input, OutputInterface $output): int
            {
                return ($this->command)($input, $output);
            }

            /**
             * @return string
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return parent::getName();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return parent::getDescription();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        return $this->run($input, $output);
    }
}
