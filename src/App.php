<?php

namespace Bermuda\App;


use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Bermuda\ServiceFactory\Factory;
use Psr\Container\ContainerInterface;


/**
 * Class App
 * @package Bermuda\App
 */
abstract class App implements AppInterface
{
    protected Factory $factory;
    protected InvokerInterface $invoker;
    protected ContainerInterface $container;

    protected string $version;
    protected array $entries = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->factory = new Factory($container->get(FactoryInterface::class));
        $this->version = $this->getVersion();
        $this->entries[AppInterface::class] = $this;
    }

    /**
     * @return string
     */
    private function getVersion(): string
    {
        return $this->container->has('app_version') ?
            $this->container->get('app_version') : '1.0.0' ;
    }

    /**
     * @param string|null $version
     * @return string
     */
    public function version(string $version = null): string
    {
        if ($version != null)
        {
            return $this->version = $version;
        }

        return $this->version;
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
    public function get($id)
    {
        return $this->entries[$id] ?? $this->container->get($id);
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
    public function call($callable, array $parameters = [])
    {
        return $this->invoker->call($callable, $parameters);
    }
}
