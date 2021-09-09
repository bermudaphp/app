<?php

namespace Bermuda\App;

use Bermuda\ErrorHandler\ErrorHandlerInterface;
use Bermuda\ServiceFactory\FactoryInterface as ServiceFactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\{ArgvInput, InputInterface};
use Symfony\Component\Console\Output\{ConsoleOutput, OutputInterface};

final class Console extends App
{
    private Console\CommandRunnerInterface $runner;
    private Console\CommandResolverInterface $resolver;

    public function __construct(ContainerInterface      $container, InvokerInterface $invoker,
                                ServiceFactoryInterface $serviceFactory, ErrorHandlerInterface $errorHandler
    )
    {
        parent::__construct($container, $invoker, $serviceFactory, $errorHandler);

        $this->runner = self::getRunner($this);
        $this->resolver = self::getResolver($this);
    }

    private static function getRunner(self $console): Console\CommandRunnerInterface
    {
        if (!$console->container->has(Console\CommandRunnerInterface::class)) {
            ($runner = new Console\SymfonyConsole())
                ->getConsole()->setName($console->name);
            $runner->getConsole()->setVersion($console->version);

            return $runner;
        }

        return $console->container->get(Console\CommandRunnerInterface::class);
    }

    private static function getResolver(self $console): Console\CommandResolverInterface
    {
        return cget($console, Console\CommandRunnerInterface::class, fn() => new Console\CommandResolver($console), true);
    }

    /**
     * @inheritDoc
     */
    public function pipe($any): AppInterface
    {
        try {
            $this->runner->add($this->resolver->resolve($any));
        } catch (Console\UnresolvableCommandException $e) {
            Console\UnresolvableCommandException::reThrow($e, debug_backtrace()[0]);
        }

        return $this;
    }

    protected function doRun(): void
    {
        $this->runner->run(self::getInput($this), self::getOutput($this));
    }

    private static function getInput(self $console): InputInterface
    {
        return cget($console, InputInterface::class, static fn() => new ArgvInput, true);
    }

    private function getOutput(self $console): OutputInterface
    {
        return cget($console, OutputInterface::class, static fn() => new ConsoleOutput, true);
    }
}
