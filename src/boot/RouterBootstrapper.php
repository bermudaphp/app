<?php

namespace Bermuda\App\Boot;


use Bermuda\App\AppInterface;
use Bermuda\Router\RouteMap;
use Bermuda\Router\RouterInterface;


/**
 * Class RouterBootstrapper
 * @package Bermuda\App\Boot
 */
final class RouterBootstrapper implements BootstrapperInterface
{
    /**
     * @param AppInterface $app
     */
    public function boot(AppInterface $app): void
    {
        PHP_SAPI === 'cli' ?:
        (static function(RouteMap $routes): void
        {
            require APP_ROOT . '\config\routes.php';
        })($app->get(RouterInterface::class));
    }
}
