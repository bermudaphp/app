<?php

namespace Bermuda\App\Boot;

use Bermuda\Config\Config;
use Bermuda\App\AppInterface;
use Bermuda\Router\RouteMap;
use Bermuda\Router\Router;
use const Bermuda\App\is_cli;

final class RouterBootstrapper implements BootstrapperInterface
{
    /**
     * @inerhitDoc
     */
    public function boot(AppInterface $app): AppInterface
    {
        if (!is_cli) $this($app);
        return $app;
    }

    public function __invoke(AppInterface $app): RouteMap
    {
        $app->registerAlias('router', Router::class);

        $routes = (static function (RouteMap $routes) use ($app): RouteMap {
            if (Config::$devMode) {
                require '.\config\routes.php';
            } else {
                $routes = $routes::createFromCache('.\config\cache\routes.php', compact('app'));
            }

            return $routes;
        })($app->get(Router::class)->getRoutes());

        $app->extend(Router::class, static function(Router $router) use ($routes): Router {
            return $router->withRoutes($routes);
        });

        return $routes;
    }
}
