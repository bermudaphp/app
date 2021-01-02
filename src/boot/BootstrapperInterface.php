<?php

namespace Bermuda\App\Boot;


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
    public function boot(AppInterface $app): void ;
}
