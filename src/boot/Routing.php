<?php

namespace Bermuda\App\Boot;

use Bermuda\Config\Config;
use Bermuda\App\AppInterface;
use Bermuda\Router\RouteMap;
use Bermuda\Router\Router;

final class Routing implements Bootable
{
    /**
     * @inerhitDoc
     */
    public function boot(AppInterface $app): void
    {
        $this->loadRoutes($app);
    }

    public function loadRoutes(AppInterface $app): RouteMap
    {
        $app->registerAlias('router', Router::class);

        $routes = (static function (RouteMap $routes) use ($app): RouteMap {
            if (Config::$devMode) {
                require_once '.\config\routes.php';
            } else {
                $routes = $routes::createFromCache('.\config\cache\routes.php', compact('app'));
                $app->extend(Router::class, static function (Router $router) use ($routes): Router {
                    return $router->withRoutes($routes);
                });
            }

            return $routes;
        })($app->get(RouteMap::class));

        return $routes;
    }
}
