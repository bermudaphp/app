<?php


namespace Bermuda\App;


use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Bermuda\App\AppInterface;
use Bermuda\ServiceFactory\FactoryException;
use Bermuda\ErrorHandler\ErrorResponseGenerator;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Container\ContainerInterface;
use Webmozart\PathUtil\Path;


/**
 * Class App
 * @package Bermuda\App
 */
class App implements AppInterface
{
    private array $entries = [];

    public function __construct(ContainerInterface $container)
    {
        $this->entries[ContainerInterface::class] = $container;

        $this->inject(Pipeline::class)
            ->inject(EmitterInterface::class)
            ->inject(ServerRequestCreator::class)
            ->inject(ErrorResponseGenerator::class)
            ->inject(InvokerInterface::class)
            ->inject(FactoryInterface::class)
            ->inject(Resolver::class)
            ->injectRunner();

        $this->entries['app.root'] = $container->get('app.root');
    }


    /**
     * @param string $classname
     * @return object
     */
    private function inject(string $classname) : self
    {
        $service = $this->entries[ContainerInterface::class]->get($classname);

        if (!$service instanceof $classname)
        {
            throw new \RuntimeException();
        }

        $this->entries[$classname] = $service;

        return $this;
    }

    /**
     * @return $this
     */
    public function injectRunner() : self
    {
        $this->entries[RequestHandlerRunner::class] = new RequestHandlerRunner(
            $this->entries[Pipeline::class],
            $this->entries[EmitterInterface::class],
            $this->entries[ServerRequestCreator::class],
            $this->entries[ErrorResponseGenerator::class]
        );

        return $this;
    }

    /**
     * @param string $service
     * @param array $params
     * @return object
     * @throws FactoryException
     */
    public function __invoke(string $service, array $params = []): object
    {
        return $this->make($service, $params);
    }

    /**
     * @param string $service
     * @param array $params
     * @return object
     * @throws FactoryException
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
     * @param string $id
     * @param $value
     * @return Application
     */
    public function set(string $id, $value): Application
    {
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
    public function call($callable, array $parameters = array())
    {
        return $this->entries[InvokerInterface::class]->call($callable, $parameters);
    }

    /**
     * @param $middleware
     * @return Application
     * @throws UnresolvableMiddlewareException
     */
    public function pipe($middleware): Application
    {
        $this->entries[Pipeline::class]->pipe($this->entries[Resolver::class]
            ->resolve($middleware)
        );

        return $this;
    }

    /**
     * @param string ...$segments
     * @return string
     */
    public function path(string ...$segments): string
    {
        array_unshift($segments, $this->entries['app.root']);
        return Path::join($segments);
    }
}
