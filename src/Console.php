<?php

namespace Bermuda\App;


use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class Console
 * @package Bermuda\App
 */
final class Console extends App
{
    private Console\CommandRunnerInterface $runner;
    private Console\CommandResolverInterface $resolver;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->runner = $this->getRunner();
        $this->resolver = $this->getResolver();
    }

    /**
     * @param mixed $any
     * @return $this|AppInterface
     */
    public function pipe($any): AppInterface
    {
        $this->runner->add($this->resolver->resolve($any));
        return $this;
    }

    public function run(): void
    {
        $this->runner->run($this->getInput(), $this->getOutput());
    }

    /**
     * @return InputInterface
     */
    private function getInput(): InputInterface
    {
        return $this->container->has(InputInterface::class)
            ? $this->container->get(InputInterface::class) :
            new ArgvInput();
    }

    /**
     * @return OutputInterface
     */
    private function getOutput(): OutputInterface
    {
        return $this->container->has(OutputInterface::class)
            ? $this->container->get(OutputInterface::class) :
            new ConsoleOutput();
    }

    /**
     * @return Console\CommandRunnerInterface
     */
    private function getRunner(): Console\CommandRunnerInterface
    {
        if (!$this->container->has(CommandRunnerInterface::class))
        {
            ($runner = new Console\SymfonyConsole())
                ->getConsole()->setName($this->name);

            $runner->getConsole()->setVersion($this->version);

            return $runner;
        }

        return $this->container->get(CommandRunnerInterface::class);
    }

    /**
     * @return Console\CommandResolverInterface
     */
    private function getResolver(): Console\CommandResolverInterface
    {
        return $this->container->has(CommandRunnerInterface::class)
            ? $this->container->get(CommandRunnerInterface::class) :
            new Console\CommandResolver($this->container);
    }
}
