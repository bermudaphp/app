<?php

namespace Bermuda\App\Boot;

use Generator;
use Bermuda\App\AppInterface;
use Bermuda\ErrorHandler\{ErrorHandler, ConfigProvider};
use Bermuda\ErrorHandler\Listener\ErrorListenerInterface;

final class ErrorHandling implements Bootable
{
    /**
     * @inerhitDoc 
     */
    public function boot(AppInterface $app): void
    {
        foreach ($this->getListeners($app) as $listener) $app->errorHandler->listen($listener);
    }
   
    private function getListeners(AppInterface $app): Generator
    {
        foreach($app->config->get(ConfigProvider::CONFIG_KEY_ERROR_LISTENERS, []) as $listener) {
            yield $listener instanceof ErrorListenerInterface ? $listener : $app->get($listener);
        }
    }
}
