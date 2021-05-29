<?php

namespace Bermuda\App;

use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Bermuda\ServiceFactory\Factory;
use Psr\Container\ContainerInterface;
use Bermuda\App\Boot\BootstrapperInterface;
use Bermuda\ErrorHandler\ErrorHandlerInterface;

/**
 * Class App
 * @package Bermuda\App
 * @property string $name;
 * @property string $version;
 */
abstract class App implements AppInterface
{
    protected Factory $factory;
    protected InvokerInterface $invoker;
    protected ContainerInterface $container;
    protected ErrorHandlerInterface $errorHandler;

    protected string $name;
    protected string $version;
    
    protected array $entries = [];
    
    private bool $booted = false;
    private bool $runned = false;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->invoker = $container->get(InvokerInterface::class);
        $this->factory = new Factory($container->get(FactoryInterface::class));
        $this->errorHandler = $container->get(ErrorHandlerInterface::class);
        $this->name = $this->getName();
        $this->version = $this->getVersion();
        $this->entries[AppInterface::class]
            = $this->entries[ContainerInterface::class]
            = $this->entries[FactoryInterface::class]
            = $this->entries[InvokerInterface::class]
            = $this;
    }

    /**
     * @param $name
     * @return string|null
     */
    public function __get($name):? string
    {
        return $name == 'name' || $name == 'version'
            ? $this->{$name}() : null ;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value): void
    {
        if ($name == 'name' || $name == 'version')
        {
            $this->{$name}($value);
        }
    }

    /**
     * @return string
     */
    private function getName(): string
    {
        return $this->container->has('app_name') ?
            $this->container->get('app_name') : 'Bermuda' ;
    }

    /**
     * @return string
     */
    private function getVersion(): string
    {
        return $this->container->get('config')['app_version'] ?? '1.0' ;
    }
    
    /**
     * @param string|null $name
     * @return string
     */
    public function name(?string $name = null): string
    {
        return $name != null ? $this->name = $name : $this->name;
    }

    /**
     * @param string|null $version
     * @return string
     */
    public function version(string $version = null): string
    {
        return $version != null ? $this->version = $version : $this->version;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(string $service, array $params = []): object
    {
        return $this->factory->make($service, $params);
    }

    /**
     * @inheritDoc
     */
    public function make(string $service, array $params = []): object
    {
        return $this->factory->make($service, $params);
    }

    /**
     * @inheritDoc
     */
    public function set(string $id, $value): AppInterface
    {
        if ($this->has($id))
        {
            throw new \RuntimeException(sprintf('Entry with id: %s already exists in the container', $id));
        }

        $this->entries[$id] = $value;
        return $this;
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
    
    public function getIfExists(string $id, $default = null)
    {
        return $this->has($id) ? $this->get($id) : $default;
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
    final public function run():void
    {
        if ($this->runned)
        {
            throw new \RuntimeException('App is already runned');
        }
        
        $this->runned = true;
        $this->doRun();
    }
    
    /**
     * @inheritDoc
     */
    final public function boot(): void
    {
        if ($this->booted)
        {
            throw new \RuntimeException('App is already booted!');
        }
        
        $this->booted = true;
        $this->get(BootstrapperInterface::class)->boot($this);
    }
    
    /**
     * @inheritDoc
     */
    public function handleException(\Throwable $e): void
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
    
    abstract protected function doRun(): void ;
}
