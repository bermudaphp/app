<?php

namespace Bermuda\App;

use Bermuda\App\Exceptions\AppException;
use Bermuda\App\Exceptions\BadMethodCallException;
use Throwable;
use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ServiceFactory\{
    Factory as ServiceFactory,
    FactoryInterface as ServiceFactoryInterface
};
use Bermuda\ErrorHandler\ErrorHandlerInterface;
use function Bermuda\Config\cget;

abstract class App implements AppInterface
{
    protected array $entries = [];
    protected array $callbacks = [];
    protected array $aliases = [];
    
    private bool $isRun = false;

    protected Config $config;

    public function __construct(protected ContainerInterface      $container, protected InvokerInterface $invoker,
                                protected ServiceFactoryInterface $serviceFactory, protected ErrorHandlerInterface $errorHandler
    )
    {
        $this->config = Config::makeFrom($container);
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
            static fn() => new ServiceFactory($container->get(FactoryInterface::class), true)
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
            throw AppException::entryExists($id);
        }

        $this->entries[$id] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
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
        if (isset($this->aliases[$id])) {
            $id = $this->aliases[$id];
        }

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
            return $this->call($this->callbacks[$name], $arguments);
        }

        throw new BadMethodCallException('Callback [%s] not registered in app', $name);
    }

    public function __get(string $name)
    {
        if ($name === 'config') {
            return $this->config;
        }
        
        return $this->get($name);
    }

    /**
     * @param string $alias
     * @param string $link
     * @return AppInterface
     * @throws AppException
     */
    public function registerAlias(string $alias, string $link): AppInterface
    {
        if (array_key_exists($alias, $this->entries)) {
            throw AppException::entryExists($alias);
        } elseif (isset($this->aliases[$alias])) {
            throw AppException::aliasExists($alias);
        }

        $this->aliases[$alias] = $link;

        return $this;
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
