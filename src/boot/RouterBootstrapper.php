<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\Router\Route;
use Bermuda\Router\RouteMap;
use Bermuda\Router\Router;
use function Bermuda\App\is_console_sapi;

final class RouterBootstrapper implements BootstrapperInterface
{
    /**
     * @inerhitDoc
     */
    public function boot(AppInterface $app): void
    {
        if (!is_console_sapi()) $this($app);
    }

    public function __invoke(AppInterface $app): RouteMap
    {
        return (static function (RouteMap $routes) use ($app): RouteMap {
            require_once '.\config\routes.php';
            return $routes;
        })($app->registerAlias('router', Router::class)->router->getRoutes());
    }
}
