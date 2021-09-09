<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\ErrorHandler\{ErrorHandlerInterface, ErrorListenerInterface, ErrorHandler};

final class ErrorHandlerBootstrapper implements BootstrapperInterface
{
    protected iterable $listeners;

    public function __construct(iterable $listeners)
    {
        $this->listeners = $listeners;
    }

    /**
     * @inerhitDoc 
     */
    public function boot(AppInterface $app): void
    {
        $handler = self::getErrorHandler($app);

        if ($handler instanceof ErrorHandler) {
            foreach ($this->getListeners($app) as $listener) {
                $handler->listen($listener);
            }
        }
    }
    
    private static function getErrorHandler(AppInterface $app): ErrorHandlerInterface
    {
        return $app->get(ErrorHandlerInterface::class);
    }

    private function getListeners(AppInterface $app): \Generator
    {
        foreach ($this->listeners as $listener) {
            if (!$listener instanceof ErrorListenerInterface) {
                $listener = $app->get($listener);
            }
            
            yield $listener;
        }
    }
}
