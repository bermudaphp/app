<?php

namespace Bermuda\App;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Bermuda\App\Console\UnresolvableCommandException;

/**
 * Class Console
 * @package Bermuda\App
 */
final class Console extends App
{
    private Console\CommandRunnerInterface $runner;
    private Console\CommandResolverInterface $resolver;

    public function __construct(ContainerInterface $container, InvokerInterface $invoker, 
        ServiceFactoryInterface $serviceFactory, ErrorHandlerInterface $errorHandler,
        BootstrapperInterface $bootstrapper, ?string $name = null, ?string $version = null
    )
    {
        parent::__construct($container, $invoker, $serviceFactory,
            $errorHandler, $bootstrapper, $name, $version
        );
 
        $this->runner = $this->getRunner(); 
        $this->resolver = $this->getResolver();
    }
    
    /**
     * @inheritDoc
     */
    public function pipe($any): AppInterface
    {
        try {
            $this->runner->add($this->resolver->resolve($any));
        }

        catch (UnresolvableCommandException $e)
        {
            UnresolvableCommandException::reThrow($e, debug_backtrace()[0]);
        }

        return $this;
    }

    protected function doRun(): void
    {
        $this->runner->run($this->getInput(), $this->getOutput());
    }

    /**
     * @return InputInterface
     */
    private function getInput(): InputInterface
    {
        return containerGet($this, InputInterface::class, static fn() => new ArgvInput, true);
    }

    /**
     * @return OutputInterface
     */
    private function getOutput(): OutputInterface
    {
         return containerGet($this, OutputInterface::class, static fn() => new ConsoleOutput, true);
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
        return containerGet($this, CommandRunnerInterface::class, fn() => new Console\CommandResolver($this->container), true);
    }
}
