<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;

interface Bootable
{
    /**
     * Application boot
     * @param AppInterface $app
     * @return AppInterface
     */
    public function boot(AppInterface $app): void ;
}
