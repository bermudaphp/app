<?php

namespace Bermuda\App;

use Throwable;
use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ServiceFactory\{
    Factory as ServiceFactory,
    FactoryInterface as ServiceFactoryInterface
};
use Bermuda\ErrorHandler\ErrorHandlerInterface;

abstract class App implements AppInterface
{
    protected array $entries = [];
    protected array $callbacks = [];
    
    private bool $isRun = false;

    protected Config $config;

    public function __construct(protected ContainerInterface      $container, protected InvokerInterface $invoker,
                                protected ServiceFactoryInterface $serviceFactory, protected ErrorHandlerInterface $errorHandler
    )
    {
        $this->config = Config::makeFrom($container);
        $this->name = $this->config[static::appNameID];
        $this->version = $this->config[static::appVersionID];
        $this->bindEntries();
    }

    protected function bindEntries(): void
    {
        $this->entries[AppInterface::class]
            = $this->entries[ContainerInterface::class]
            = $this->entries[FactoryInterface::class]
            = $this->entries[ServiceFactoryInterface::class]
            = $this->entries[InvokerInterface::class]
            = $this;
        $this->entries[Config::class] = $this->config;
    }

    public static function makeFrom(ContainerInterface $container): self
    {
        return new static($container, $container->get(InvokerInterface::class),
            static::getServiceFactory($container), $container->get(ErrorHandlerInterface::class)
        );
    }

    protected static function getServiceFactory(ContainerInterface $container): ServiceFactoryInterface
    {
        return cget($container, ServiceFactoryInterface::class,
            static fn() => new ServiceFactory($container->get(FactoryInterface::class))
        );
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(string $service, array $params = []): object
    {
        return $this->serviceFactory->make($service, $params);
    }

    /**
     * @inheritDoc
     */
    public function make(string $service, array $params = []): object
    {
        return $this->serviceFactory->make($service, $params);
    }

    /**
     * @inheritDoc
     */
    public function set(string $id, $value): AppInterface
    {
        if ($this->has($id)) {
            AppException::entryExists($id);
        }

        $this->entries[$id] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return array_key_exists($id, $this->entries) || $this->container->has($id);
    }

    /**
     * @inheritDoc
     */
    public function extend(string $id, callable $extender): self
    {
        $this->entries[$id] = $extender($this->get($id));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->entries[$id] ?? $this->container->get($id);
    }

    /**
     * @inheritDoc
     */
    final public function run(): void
    {
        if ($this->isRun) {
            throw AppException::isRun();
        }

        $this->isRun = true;
        $this->doRun();
    }

    abstract protected function doRun(): void;

    /**
     * @inheritDoc
     */
    public function handleException(Throwable $e): void
    {
        $this->errorHandler->handleException($e);
    }

    /**
     * @inheritDoc
     */
    public function call($callable, array $parameters = [])
    {
        return $this->invoker->call($callable, $parameters);
    }
    
     /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (isset($this->callbacks[$name])) {
            return $this->call(callbacks[$name], $arguments);
        }

        throw BadMethodCallException::doesntExists($name);
    }

    /**
     * @param string $name
     * @param callable $callback
     * @return AppInterface
     */
    public function registerCallback(string $name, callable $callback): AppInterface
    {
        if (!isset($this->callbacks[$name])) {
            $this->callbacks[$name] = $callback;
            return $this;
        }

        throw AppException::callback($name);
    }
}
