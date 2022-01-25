<?php

namespace Bermuda\App;

use DI\Container;
use DI\Definition\Source\MutableDefinitionSource;
use DI\Proxy\ProxyFactory;
use Throwable;
use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ErrorHandler\ErrorHandlerInterface;
use Bermuda\App\Exceptions\AppException;
use Bermuda\App\Exceptions\BadMethodCallException;

abstract class App extends Container implements AppInterface
{
    private bool $isRun = false;

    protected Config $config;

    protected array $callbacks = [];
    protected array $aliases = [];

    protected ErrorHandlerInterface $errorHandler;

    public function __construct(
        MutableDefinitionSource $definitionSource = null,
        ProxyFactory $proxyFactory = null,
        ContainerInterface $wrapperContainer = null
    ){
        parent::__construct($definitionSource, $proxyFactory, $wrapperContainer);
        $this->bindEntries();
    }

    protected function bindEntries(): void
    {
        $this->resolvedEntries[AppInterface::class] = $this;
        $this->resolvedEntries[Config::class] = $this->config = Config::createConfig($this);
        $this->errorHandler = $this->get(ErrorHandlerInterface::class);
    }

    /**
     * @param ContainerInterface $container
     * @return static
     */
    public static function createApp(ContainerInterface $container): self
    {
        return new static(null, null, $container);
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
    public function make($name, array $params = []): mixed
    {
        return parent::make($name, $params);
    }

    /**
     * @inerhitDoc
     */
    public function get($name)
    {
        if (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }

        return parent::get($name);
    }

    /**
     * @inerhitDoc
     */
    public function has($name): bool
    {
        return parent::has($name) || isset($this->aliases[$name]);
    }

    /**
     * @param string $id
     * @param $value
     * @return AppInterface
     */
    public function set(string $id, $value): AppInterface
    {
        if ($this->has($id)) {
            throw AppException::entryExists($id);
        }

        parent::set($id, $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function extend(string $id, callable $extender): self
    {
        $entry = $this->get($id);
        $this->resolvedEntries[$id] = $extender($entry, $this);
        return $this;
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
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (isset($this->callbacks[$name])) {
            return $this->call($this->callbacks[$name], $arguments);
        }

        throw BadMethodCallException::create('Callback [ %s ] not registered in app', $name);
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
        if (array_key_exists($alias, $this->resolvedEntries)) {
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
