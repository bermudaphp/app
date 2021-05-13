<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;

/**
 * Class PipelineBootstrapper
 * @package Bermuda\App\Boot
 */
final class PipelineBootstrapper implements BootstrapperInterface
{
    public function boot(AppInterface $app): void
    {
        (static fn() =>
            require APP_ROOT . '\config\\' . (PHP_SAPI === 'cli' ? 'commands.php' : 'pipeline.php' );
        )();
    }
}
