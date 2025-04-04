<?php

namespace Bermuda\App\Listeners;

use Bermuda\Router\RouteMap;
use Bermuda\App\AppInterface;
use Bermuda\Router\RouteRecord;
use Bermuda\Router\Attribute\Route;
use Bermuda\Reflection\ReflectionClass;
use Bermuda\Reflection\ReflectionFunction;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Bermuda\ClassFinder\FinalizedListenerInterface;

class RegisterRouteListener implements FinalizedListenerInterface
{
    private RouteMap $map;

    /**
     * @var array<int, array{0:class-string, 1: Route}>
     */
    private array $routes = [];

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        private readonly AppInterface $app
    ) {
        $this->map = $this->app->get(RouteMap::class);
    }

    public function finalize(): void
    {
        krsort($this->routes);
        foreach ($this->routes as $priorityGroup) {
            foreach ($priorityGroup as $pair) {
                /**
                 * @var Route $route
                 */
                list($handler, $route) = $pair;

                $routeRecord = RouteRecord::fromArray([
                    'handler' => $handler,
                    'name' => $route->name,
                    'path' => $route->path,
                    'middleware' => $route->middleware,
                    'methods' => $route->methods,
                    'defaults' => $route->defaults,
                ]);

                if ($route->group) {
                    $this->map->group($route->group)->addRoute($routeRecord);
                } else {
                    $this->map->addRoute($routeRecord);
                }
            }
        }
    }

    public function handle(ReflectionClass|ReflectionFunction $reflector): void
    {
        foreach ($reflector->getAttribute(Route::class, true) ?? [] as $attribute) {
            /**
             * @var Route $route
             */
            $route = $attribute->newInstance();
            $target = $attribute->target;

            if ($target instanceof \ReflectionMethod) {
                $this->routes[$route->priority][] = ["$target->class@$target->name", $route];
            } else $this->routes[$route->priority][] = [$target->name, $route];
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function createFromContainer(ContainerInterface $container): self
    {
        return new self(
            $container->get(AppInterface::class)
        );
    }
}
