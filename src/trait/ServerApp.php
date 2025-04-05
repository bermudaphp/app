<?php

namespace Bermuda\App\Trait;

use Bermuda\App\AppInterface;
use Bermuda\MiddlewareFactory\MiddlewareFactoryInterface;
use Bermuda\MiddlewareFactory\UnresolvableMiddlewareException;
use Bermuda\Pipeline\PipelineInterface;
use DI\Definition\Source\MutableDefinitionSource;
use DI\Proxy\ProxyFactory;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait ServerApp
{
    use App { bindEntries as bindEntriesSuper; }

    protected PipelineInterface $pipeline;
    protected EmitterInterface $emitter;
    protected MiddlewareFactoryInterface $middlewareFactory;
    protected ServerRequestCreatorInterface $serverRequestCreator;

    protected function bindEntries(): void
    {
        $this->bindEntriesSuper();
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
            $this->pipeline->pipe($this->middlewareFactory->makeMiddleware($any));
        } catch (UnresolvableMiddlewareException $e) {
            throw $e->setBacktrace(debug_backtrace()[0]);
        }

        return $this;
    }
}
