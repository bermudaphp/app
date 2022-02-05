<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\Router\RouteMap;
use Bermuda\Router\Router;
use function Bermuda\App\is_cli;

final class RouterBootstrapper implements BootstrapperInterface
{
    /**
     * @inerhitDoc
     */
    public function boot(AppInterface $app): void
    {
        if (!is_cli()) $this($app);
    }

    public function __invoke(AppInterface $app): RouteMap
    {
        $app->registerAlias('router', Router::class);

        $routes = (static function (RouteMap $routes) use ($app): RouteMap {
            require_once '.\config\routes.php';
            return $routes;
        })($app->get(Router::class)->getRoutes());

        $app->extend(Router::class, static function(Router $router) use ($routes): Router {
            return $router->withRoutes($routes);
        });

        return $routes;
    }
}
