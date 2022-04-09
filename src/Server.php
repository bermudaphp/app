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
    private EmitterInterface $emitter;
    private MiddlewareFactoryInterface $middlewareFactory;
    private ServerRequestCreatorInterface $serverRequestCreator;

    protected function bindEntries(): void
    {
        parent::bindEntries();
        $this->pipeline = $this->get(PipelineInterface::class);
        $this->emitter = $this->get(EmitterInterface::class);
        $this->middlewareFactory = $this->get(MiddlewareFactoryInterface::class);
        $this->serverRequestCreator = $this->get(ServerRequestCreatorInterface::class);
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
     * @throws Throwable 
     */
    protected function doRun(): void
    {
        $request = $this->serverRequestCreator->fromGlobals();
        
        try {
            $response = $this->pipeline->handle($request);
            $this->emitter->emit($response);
        } catch(Throwable $e) {
            $this->errorHandler->setServerRequest($request);
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function pipe(mixed $any): AppInterface
    {
        try {
            $this->pipeline->pipe($this->middlewareFactory->make($any));
        } catch (UnresolvableMiddlewareException $e) {
            UnresolvableMiddlewareException::reThrow($e, debug_backtrace()[0]);
        }

        return $this;
    }
}
