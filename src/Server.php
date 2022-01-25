<?php

namespace Bermuda\App;

use DI\FactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
use Psr\Http\Message\StreamFactoryInterface;

final class Server extends App implements RequestHandlerInterface
{
    private PipelineInterface $pipeline;

    public function __construct(ContainerInterface                    $container, InvokerInterface $invoker,
                                FactoryInterface               $factory, ErrorHandlerInterface $errorHandler,
                                private EmitterInterface              $emitter, private MiddlewareFactoryInterface $middlewareFactory,
                                private ServerRequestCreatorInterface $serverRequestCreator,
    )
    {
        parent::__construct($container, $invoker, $factory, $errorHandler);
        $this->pipeline = $this->make(PipelineInterface::class);
    }

    public static function createApp(ContainerInterface $container): Server
    {
        return new static($container, $container->get(InvokerInterface::class),
            $container->get(FactoryInterface::class), $container->get(ErrorHandlerInterface::class), $container->get(EmitterInterface::class),
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
        $request = $this->serverRequestCreator->fromGlobals();
        
        try {
            $response = $this->pipeline->handle($request);
            $this->emitter->emit($response);
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
