<?php

namespace Bermuda\App;

use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Bermuda\ServiceFactory\Factory;
use Psr\Container\ContainerInterface;
use Bermuda\Pipeline\PipelineFactory;
use Bermuda\Pipeline\PipelineInterface;
use Bermuda\ServiceFactory\FactoryException;
use Bermuda\ErrorHandler\RequestHandlingException;
use Bermuda\MiddlewareFactory\MiddlewareFactoryInterface;
use Bermuda\MiddlewareFactory\UnresolvableMiddlewareException;

/**
 * Class Server
 * @package Bermuda\App
 */
final class Server extends App
{
    private EmitterInterface $emitter;
    private PipelineInterface $pipeline;
    private ResponseFactoryInterface $responseFactory;
    private MiddlewareFactoryInterface $middlewareFactory;
    private ServerRequestCreatorInterface $serverRequestCreator;
    
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->pipeline = $container->get(PipelineInterface::class);
        $this->emitter = $container->get(EmitterInterface::class);
        $this->serverRequestCreator = $container->get(ServerRequestCreatorInterface::class);
        $this->responseFactory = $container->get(ResponseFactoryInterface::class);
        $this->middlewareFactory = $container->get(MiddlewareFactoryInterface::class);
    }

    /**
     * Run application
     * @throws RequestHandlingException if request handling is failure
     * @throws \Throwable if request creation is failure
     */
    protected function doRun(): void
    {
        $request = $this->serverRequestCreator->fromGlobals();
        
        try
        {
            $this->emitter->emit($this->pipeline->handle($request));
        }
        
        catch(\Throwable $e)
        {
            throw new RequestHandlingException($e, $request);
        }
    }

    /**
     * @inheritDoc
     */
    public function pipe($any): AppInterface
    {
        try
        {
            $this->pipeline->pipe($this->middlewareFactory->make($any));
        }
        
        catch (UnresolvableMiddlewareException $e)
        {
            UnresolvableMiddlewareException::reThrow($e, debug_backtrace()[0]);
        }

        return $this;
    }
}
