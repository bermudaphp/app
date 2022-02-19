<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;

/**
 * Interface BootstrapperInterface
 * @package Bermuda\App\Boot
 */
interface BootstrapperInterface
{
    /**
     * Application boot
     * @param AppInterface $app
     */
    public function boot(AppInterface $app): AppInterface ;
}
