<?php


namespace Bermuda\App;


use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\Pipeline\PipelineInterface;
use Bermuda\ServiceFactory\FactoryException;
use Bermuda\ErrorHandler\ErrorResponseGenerator;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Bermuda\MiddlewareFactory\MiddlewareFactoryInterface;


/**
 * Class App
 * @package Bermuda\App
 */
class App implements AppInterface
{
    private array $entries = [];

    public function __construct(AppFactory $factory)
    {
        $this->entries = $factory->getEntries();
    }

    /**
     * @inheritDoc
     */
    public function __invoke(string $service, array $params = []): object
    {
        return $this->make($service, $params);
    }

    /**
     * @inheritDoc
     */
    public function make(string $service, array $params = []): object
    {
        try
        {
            $service = $this->entries[FactoryInterface::class]->make($service, $params);
        }

        catch (\Throwable $e)
        {
            FactoryException::wrap($e)->throw();
        }

        return $service;
    }

    /**
     * Run application
     */
    public function run(): void
    {
        $this->entries[RequestHandlerRunner::class]->run();
    }

    /**
     * @inheritDoc
     */
    public function set(string $id, $value): AppInterface
    {
        if($this->has($id))
        {
            throw \RuntimeException(sprintf('Entry with id: %s already exists in the container', $id));
        }
        
        $this->entries[$id] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->entries[$id] ?? $this->entries[ContainerInterface::class]->get($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return array_key_exists($id, $this->entries) || $this->entries[ContainerInterface::class]->has($id);
    }

    /**
     * @inheritDoc
     */
    public function call($callable, array $parameters = [])
    {
        return $this->entries[InvokerInterface::class]->call($callable, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function pipe($any): AppInterface
    {
        $this->entries[PipelineInterface::class]->pipe($this->entries[MiddlewareFactoryInterface::class]->make($any));
        return $this;
    }
}
