<?php


namespace Bermuda\App;


use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Bermuda\Registry\Registry;
use Psr\Container\ContainerInterface;
use Bermuda\Pipeline\PipelineInterface;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Bermuda\MiddlewareFactory\MiddlewareFactoryInterface;


/**
 * Class AppFactory
 * @package Bermuda\App
 */
final class AppFactory
{
    private array $entries = [];
    
    public function __invoke(ContainerInterface $container): AppInterface
    {
        return Registry::set(AppInterface::class, $this->make($container));
    }

    private function make(ContainerInterface $c): AppInterface
    {
        if ($c->has(AppInterface::class))
        {
            return $c->get(AppInterface::class);
        }
        
        $config = $c->get('config');
        $version = isset($config['app']) ? $config->get('version', '1.0') : '1.0';

        return new App($this->withEntries($c, $c->get(RequestHandlerRunner::class),
            $c->get(PipelineInterface::class), $c->get(FactoryInterface::class),
            $c->get(InvokerInterface::class), $c->get(MiddlewareFactoryInterface::class)
        ), $version);
    }
    
    /**
     * @return array
     */
    public function getEntries(): array
    {
        return $this->entries;
    }
    
    /**
     * @param ContainerInterface $container
     * @return $this
     */
    private function withEntries(
        ContainerInterface $container, RequestHandlerRunner $runner,
        PipelineInterface $pipeline, FactoryInterface $factory,
        InvokerInterface $invoker, MiddlewareFactoryInterface $middlewareFactory
    ): self
    {
        $copy = clone $this;

        $copy->entries[ContainerInterface::class] = $container;
        $copy->entries[RequestHandlerRunner::class] = $runner;
        $copy->entries[PipelineInterface::class] = $pipeline;
        $copy->entries[FactoryInterface::class] = $factory;
        $copy->entries[InvokerInterface::class] = $invoker;
        $copy->entries[MiddlewareFactoryInterface::class] = $middlewareFactory;

        return $copy;
    }
}
