<?php

namespace Bermuda\App;

use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ErrorHandler\ErrorHandlerInterface;
use Symfony\Component\Console\Input\{ArgvInput, InputInterface};
use Symfony\Component\Console\Output\{ConsoleOutput, OutputInterface};
use Throwable;

class Console extends App
{
    protected Console\CommandRunnerInterface $runner;
    protected Console\CommandResolverInterface $resolver;

    protected function bindEntries(): void
    {
        parent::bindEntries();
        $this->runner = $this->get(Console\CommandRunnerInterface::class);
        $this->resolver = $this->get(Console\CommandResolverInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function pipe(mixed $any): AppInterface
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
        $this->runner->run($this->get(InputInterface::class), $this->get(OutputInterface::class));
    }
}
