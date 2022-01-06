<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\ErrorHandler\{ErrorHandlerInterface, ErrorListenerInterface, ErrorHandler};

final class ErrorHandlerBootstrapper implements BootstrapperInterface
{
    public function __construct(private iterable $listeners = [])
    {
        $this->listeners = $listeners;
    }

    /**
     * @param iterable $listeners
     * @return $this
     */
    public function setListeners(iterable  $listeners): self
    {
        $this->listeners = $listeners;
        return $this;
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
        foreach ($this->listeners as $listener) yield !$listener instanceof ErrorListenerInterface 
            ? $app->get($listener) : $listener ;
    }
}
