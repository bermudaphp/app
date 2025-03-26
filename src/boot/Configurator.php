<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\Config\ConfigProvider;

class Configurator implements Bootable
{
    public function boot(AppInterface $app): void
    {
        foreach ($app->config->get(ConfigProvider::bootstrap, []) as $key => $bootable) {
            if (is_string($bootable)) $bootable = $app->get($bootable);
            if (!$bootable instanceof \Bermuda\App\Boot\Bootable) {
                throw new \RuntimeException("Invalid bootable provided for key: $key");
            }

            $bootable->boot($app);
        }
    }
}