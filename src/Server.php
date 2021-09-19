<?php

namespace Bermuda\App;

use Throwable;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\Pipeline\PipelineInterface;
use Bermuda\ErrorHandler\{
    ServerException,
    ErrorHandlerInterface
};
use Bermuda\MiddlewareFactory\{
    MiddlewareFactoryInterface, 
    UnresolvableMiddlewareException
};
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Bermuda\ServiceFactory\FactoryInterface as ServiceFactoryInterface;

final class Server extends App implements RequestHandlerInterface
{
    private PipelineInterface $pipeline;

    public function __construct(ContainerInterface $container, InvokerInterface $invoker,
                                ServiceFactoryInterface $serviceFactory, ErrorHandlerInterface $errorHandler,
                                private EmitterInterface $emitter, private MiddlewareFactoryInterface $middlewareFactory,
                                private ServerRequestCreatorInterface $serverRequestCreator,
    )
    {
        parent::__construct($container, $invoker, $serviceFactory, $errorHandler);
        $this->pipeline = $this->make(PipelineInterface::class);
    }

    public static function makeFrom(ContainerInterface $container): self
    {
        return new static($container, $container->get(InvokerInterface::class),
            static::getServiceFactory($container), $container->get(ErrorHandlerInterface::class), $container->get(EmitterInterface::class),
            $container->get(MiddlewareFactoryInterface::class), $container->get(ServerRequestCreatorInterface::class)
        );
    }
    
    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->pipeline->handle($request);
    }

    /**
     * Run application
     * @throws ServerException if request handling is failure
     * @throws Throwable if request creation is failure
     */
    protected function doRun(): void
    {
        $request = $this->serverRequestCreator
            ->fromGlobals();
        
        try {
            $this->emitter->emit($this->handle($request));
        } catch(Throwable $e) {
            throw new ServerException($e, $request);
        }
    }

    /**
     * @inheritDoc
     */
    public function pipe($any): AppInterface
    {
        try {
            $this->pipeline->pipe($this->middlewareFactory->make($any));
        } catch (UnresolvableMiddlewareException $e) {
            UnresolvableMiddlewareException::reThrow($e, debug_backtrace()[0]);
        }

        return $this;
    }
}
