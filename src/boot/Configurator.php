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

            if (is_callable($bootable)) $bootable($app);
            else if ($bootable instanceof \Bermuda\App\Boot\Bootable) {
                $bootable->boot($app);
            }

            throw new \RuntimeException("Invalid bootable provided for key: $key");
        }
    }
}
