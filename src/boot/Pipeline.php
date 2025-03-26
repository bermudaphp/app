<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;

final class Pipeline implements Bootable
{
    /**
     * @inerhitDoc 
     */
    public function boot(AppInterface $app): void
    {
        require './config' . (PHP_SAPI == 'cli' ? '/commands.php' : '/pipeline.php');
    }
}
