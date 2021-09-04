<?php

namespace Bermuda\App;

use Throwable;
use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\App\Boot\BootstrapperInterface;
use Bermuda\ErrorHandler\ErrorHandlerInterface;
use Bermuda\ServiceFactory\Factory as ServiceFactory;
use Bermuda\ServiceFactory\FactoryInterface as ServiceFactoryInterface;

use function Bermuda\cget;

abstract class App implements AppInterface
{
    protected array $entries = [];
    
    private bool $booted = false;
    private bool $runned = false;
    
    private ?string $name = null;
    private ?string $version = null;
    
    protected const appNameID = 'app.name';
    protected const appVersionID = 'app.version';

    public function __construct(protected ContainerInterface $container, protected InvokerInterface $invoker, 
        protected ServiceFactoryInterface $serviceFactory, protected ErrorHandlerInterface $errorHandler,
        protected BootstrapperInterface $bootstrapper, protected ConfigInterface $config
    )
    {
        $this->bindEntries();
        $this->name = $config[static::appNameID];
        $this->version = $config[static::appVersionID];
    }
    
    protected function bindEntries(): void
    {
        $this->entries[AppInterface::class]
            = $this->entries[ContainerInterface::class]
            = $this->entries[FactoryInterface::class]
            = $this->entries[ServiceFactoryInterface::class]
            = $this->entries[InvokerInterface::class]
            = $this;
        $this->entries[ConfigInterface::class] = $this->config;
    }
    
    public static function makeFrom(ContainerInterface $container): self
    { 
        return new static($container, $container->get(InvokerInterface::class),
            static::getServiceFactory($container), $container->get(ErrorHandlerInterface::class),
            $container->get(BootstrapperInterface::class), static::getAppConfig($container)
        )
    }
    
    protected static function getServiceFactory(ContainerInterface $container): ServiceFactoryInterface
    {
        return cget($container, ServiceFactoryInterface::class,
            static fn() => new ServiceFactory($container->get(FactoryInterface::class))
        );
    }
    
    protected static function getAppConfig(ContainerInterface $container): ConfigInterface
    {
        return new Config(cget($container, 'config', []);
    }
  
    /**
     * @param $name
     * @return string|null
     */
    public function __get($name):? string
    {
        return $name == 'name' || $name == 'version'
            ? $this->config->{$name} : null ;
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
     * @param string|null $name
     * @return string
     */
    public function name(?string $name = null): string
    {
        return $name != null ? $this->name = $name : $this->name;
    }
                          
    /**
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
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
        !$this->has($id) ?: throw new AppException(
            sprintf('Entry with id: %s already exists in the container', $id)
        );
        
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
    public function get($id, $default = null, bool $invoke = false)
    {
        return $this->entries[$id] ?? cget($this->container, $id, $default, $invoke);
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
    final public function run(): void
    {
        !$this->runned ?: throw AppException::alredyRunned();
        $this->runned = true;
        $this->doRun();
    }
    
    /**
     * @inheritDoc
     */
    final public function boot(): AppInterface
    {
        !$this->booted ?: throw AppException::alredyBooted();
        
        try {
            $this->bootstrapper->boot($this);
        } catch (\Throwable $e) {
            throw AppException::fromPrev($e);
        }
        
        $this->booted = true;
        
        return $this;
    }
    
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
    
    abstract protected function doRun(): void ;
}
