<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;

final class PipelineBootstrapper implements BootstrapperInterface
{
    /**
     * @inerhitDoc 
     */
    public function boot(AppInterface $app): AppInterface
    {
        require '.\config' . (\Bermuda\App\is_cli ? '\commands.php' : '\pipeline.php' );
        return $app;
    }
}
