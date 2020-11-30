<?php

namespace Bermuda\App;


use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Bermuda\ServiceFactory\Factory;
use Psr\Container\ContainerInterface;
use Bermuda\Pipeline\PipelineFactory;
use Bermuda\Pipeline\PipelineInterface;
use Bermuda\ServiceFactory\FactoryException;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Bermuda\MiddlewareFactory\MiddlewareFactoryInterface;


/**
 * Class FastCGI
 * @package Bermuda\App
 */
final class FastCGI extends App
{
    private PipelineInterface $pipeline;
    private RequestHandlerRunner $runner;
    private MiddlewareFactoryInterface $middlewareFactory;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->runner = $container->get(RequestHandlerRunner::class);
        $this->pipeline = $container->get(PipelineInterface::class);
        $this->middlewareFactory = $container->get(MiddlewareFactoryInterface::class);
    }

    /**
     * Run application
     */
    public function run(): void
    {
        $this->runner->run();
    }

    /**
     * @inheritDoc
     */
    public function pipe($any): AppInterface
    {
        $this->pipeline->pipe($this->middlewareFactory->make($any));
        return $this;
    }
}
