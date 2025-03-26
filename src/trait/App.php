<?php

namespace Bermuda\App\Trait;

use Bermuda\App\AppInterface;
use Bermuda\App\Boot\Bootable;
use DI\Container;
use DI\Definition\Source\MutableDefinitionSource;
use DI\Proxy\ProxyFactory;
use Throwable;
use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\Config\Config;
use Bermuda\ErrorHandler\ErrorHandler;
use Bermuda\App\Exceptions\AppException;
use Bermuda\App\Exceptions\BadMethodCallException;

trait App
{
    public readonly Config $config;
    public readonly ErrorHandler $errorHandler;

    protected(set) bool $isRunned = false;

    /**
     * @var callable[]
     */
    protected array $callbacks = [];
    /**
     * @var string[]
     */
    protected array $aliases = [];

    public function __construct(
        ?MutableDefinitionSource $definitionSource = null,
        ?ProxyFactory $proxyFactory = null,
        ?ContainerInterface $wrapperContainer = null
    ){
        parent::__construct($definitionSource, $proxyFactory, $wrapperContainer);
        $this->bindEntries();
    }

    protected function bindEntries(): void
    {
        $this->resolvedEntries[AppInterface::class] = $this;
        $this->resolvedEntries[Config::class] = $this->config = Config::createConfig($this);
        $this->errorHandler = $this->get(ErrorHandler::class);
    }

    /**
     * @param ContainerInterface|null $container
     * @return static
     */
    public static function createApp(?ContainerInterface $container = null): self
    {
        return new static(wrapperContainer: $container);
    }


    /**
     * @inerhitDoc
     */
    public function get(string $name): mixed
    {
        if (isset($this->aliases[$name])) $name = $this->aliases[$name];
        return parent::get($name);
    }

    /**
     * @inerhitDoc
     */
    public function has(string $name): bool
    {
        return parent::has($name) || isset($this->aliases[$name]);
    }

    /**
     * @param string $id
     * @param $value
     * @return AppInterface
     * @throws AppException
     */
    public function set(string $id, mixed $value): void
    {
        if ($this->has($id)) throw AppException::entryExists($id);
        parent::set($id, $value);
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
    final public function run(?Bootable $bootable = null): void
    {
        if ($this->isRunned) throw AppException::isRun();
        $bootable?->boot($this);

        $this->doRun();
        $this->isRunned = true;
    }

    abstract protected function doRun(): void ;

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

    public function __get(string $name): mixed
    {
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
