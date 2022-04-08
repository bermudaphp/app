<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use const Bermuda\App\is_cli;

final class PipelineBootstrapper implements BootstrapperInterface
{
    /**
     * @inerhitDoc 
     */
    public function boot(AppInterface $app): AppInterface
    {
        require '.\config' . (is_cli ? '\commands.php' : '\pipeline.php' );
        return $app;
    }
}
