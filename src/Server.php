<?php

namespace Bermuda\App;

use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\Pipeline\PipelineInterface;
use Bermuda\ServiceFactory\FactoryException;
use Bermuda\ErrorHandler\HttpException;
use Bermuda\MiddlewareFactory\MiddlewareFactoryInterface;
use Bermuda\MiddlewareFactory\UnresolvableMiddlewareException;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Bermuda\ServiceFactory\FactoryInterface as ServiceFactoryInterface;

/**
 * Class Server
 * @package Bermuda\App
 */
final class Server extends App
{ 
    private PipelineInterface $pipeline;
    
    public function __construct(ContainerInterface $container, InvokerInterface $invoker, 
        ServiceFactoryInterface $serviceFactory, ErrorHandlerInterface $errorHandler,
        BootstrapperInterface $bootstrapper, private EmitterInterface $emitter, 
        private ResponseFactoryInterface $responseFactory,
        private MiddlewareFactoryInterface $middlewareFactory, 
        private ServerRequestCreatorInterface $serverRequestCreator,
        ?string $name = null, ?string $version = null
    )
    {
        parent::__construct($container, $invoker, $serviceFactory,
            $errorHandler, $bootstrapper, $name, $version
        );
 
        $this->pipeline = $this->make(PipelineInterface::class);
    }
    
    public static function makeFrom(ContainerInterface $container): self
    { 
        return new static($container, $container->get(InvokerInterface::class),
            static::getServiceFactory($container), $container->get(ErrorHandlerInterface::class),
            $container->get(BootstrapperInterface::class), $container->get(EmitterInterface::class),
            $container->get(ResponseFactoryInterface::class), $container->get(MiddlewareFactoryInterface::class),
            $container->get(ServerRequestCreatorInterface::class), static::getAppName($container), 
            static::getAppVersion($container)
        )
    }

    /**
     * Run application
     * @throws HttpException if request handling is failure
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
            throw new HttpException($e, $request);
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
