<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\ErrorHandler\{ErrorHandler, ErrorListenerInterface};

final class ErrorHandlerBootstrapper implements BootstrapperInterface
{
    public function __construct(private iterable $listeners = [])
    {
        $this->listeners = $listeners;
    }

    /**
     * @param ErrorListenerInterface[] $listeners
     * @return $this
     */
    public function setListeners(iterable $listeners): self
    {
        $this->listeners = $listeners;
        return $this;
    }
    
    /**
     * @inerhitDoc 
     */
    public function boot(AppInterface $app): AppInterface
    {
        $handler = $app->get(ErrorHandler::class);
        foreach ($this->getListeners($app) as $listener) $handler->on($listener);
        return $app;
    }
   
    private function getListeners(AppInterface $app): \Generator
    {
        foreach ($this->listeners as $listener) yield !$listener instanceof ErrorListenerInterface 
            ? $app->get($listener) : $listener ;
    }
}
