<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\Router\RouteMap;

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
        PHP_SAPI !== 'cli' ?: require APP_ROOT . '\config\routes.php';
    }
}
