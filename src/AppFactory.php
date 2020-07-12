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

    private function make(ContainerInterface $container): AppInterface
    {
        if ($container->has(AppInterface::class))
        {
            return $container->get(AppInterface::class);
        }

        return new App($this->setEntries($container));
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
    private function setEntries(ContainerInterface $container): self
    {
        $this->entries[RequestHandlerRunner::class] = $container->get(RequestHandlerRunner::class);
        $this->entries[PipelineInterface::class] = $container->get(PipelineInterface::class);
        $this->entries[FactoryInterface::class] = $container->get(FactoryInterface::class);
        $this->entries[InvokerInterface::class] = $container->get(InvokerInterface::class);
        $this->entries[MiddlewareFactoryInterface::class] = $container->get(MiddlewareFactoryInterface::class);
    }
}
