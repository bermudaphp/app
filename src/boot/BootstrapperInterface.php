<?php

namespace App;


use Bermuda\App\Boot;


/**
 * Interface BootstrapperInterface
 * @package App
 */
interface BootstrapperInterface
{
    /**
     * Application boot
     * @param AppInterface $app
     */
    public function boot(AppInterface $app): void ;
}
