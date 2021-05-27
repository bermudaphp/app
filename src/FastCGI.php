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
    private ServerRequestFactory $requestFactory;
    private ResponseFactoryInterface $responseFactory;
    private MiddlewareFactoryInterface $middlewareFactory;
    private EmitterInterface $emitter;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->emitter = $container->get(EmitterInterface::class);
        $this->pipeline = $container->get(PipelineInterface::class);
        $this->requestFactory = $container->get(ServerRequestFactory::class);
        $this->responseFactory = $container->get(ResponseFactoryInterface::class);
        $this->middlewareFactory = $container->get(MiddlewareFactoryInterface::class);
    }

    /**
     * Run application
     * @throws ServerRequestException if request handling is failure
     * @throws \Throwable if request creation is failure
     */
    public function run(): void
    {
        $request = $this->requestFactory->fromGlobals();
        
        try
        {
            $this->emitter->emit($this->pipeline->handle($request));
        }
        
        catch(\Throwable $e)
        {
            throw ServerRequestException::decorate($e, $request);
        }
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
