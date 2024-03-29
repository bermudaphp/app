<?php

namespace Bermuda\App\Boot;

use Generator;
use Bermuda\App\AppInterface;
use Bermuda\ErrorHandler\{ErrorHandler, ErrorListenerInterface, ConfigProvider};

final class ErrorHandlerBootstrapper implements BootstrapperInterface
{
    /**
     * @inerhitDoc 
     */
    public function boot(AppInterface $app): AppInterface
    {
        foreach ($this->getListeners($app) as $l) $app->errorHandler->on($l);
        return $app;
    }
   
    private function getListeners(AppInterface $app): Generator
    {
        $config = $app->config['error'];
        if ($config !== null && is_iterable($config['error.listeners'])) {
            foreach($config['error.listeners'] as $l) {
                yield $l instanceof ErrorListenerInterface ? $l : $app->get($l);
            }
        }
    }
}
