<?php

namespace Bermuda\App;

use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ErrorHandler\ErrorHandlerInterface;
use Symfony\Component\Console\Input\{ArgvInput, InputInterface};
use Symfony\Component\Console\Output\{ConsoleOutput, OutputInterface};

final class Console extends App
{
    public function __construct(ContainerInterface      $container, InvokerInterface $invoker,
                                FactoryInterface $factory, ErrorHandlerInterface $errorHandler,
                                private Console\CommandRunnerInterface $runner,
                                private Console\CommandResolverInterface $resolver
    )
    {
        parent::__construct($container, $invoker, $factory, $errorHandler);
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
        $this->runner->run($this->get(InputInterface::class), $this->get(OutputInterface::class));
    }
}
