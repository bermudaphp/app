<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use function Bermuda\App\is_cli;

final class PipelineBootstrapper implements BootstrapperInterface
{
    /**
     * @inerhitDoc 
     */
    public function boot(AppInterface $app): void
    {
        require '.\config' . (is_cli() ? '\commands.php' : '\pipeline.php' );
    }
}
