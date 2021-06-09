<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\Router\RouteMap;
use function Bermuda\is_console_sapi;

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
        is_console_sapi() ?: $this($app);
    }
    
    public function __invoke(AppInterface $app): RouteMap
    {
        return $app->call(static function(RouteMap $routes)
        {
            require_once APP_ROOT . '\config\routes.php';
            return $routes;
        });
    }
}
