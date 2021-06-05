<?php

namespace Bermuda\App\Boot;

use Bermuda\App\AppInterface;
use Bermuda\ErrorHandler\ErrorHandler;
use Bermuda\ErrorHandler\ErrorHandlerInterface;
use Bermuda\ErrorHandler\ErrorListenerInterface;

/**
 * Class ErrorHandlerBootstrapper
 * @package Bermuda\App\Boot
 */
class ErrorHandlerBootstrapper implements BootstrapperInterface
{
    protected iterable $listeners;

    public function __construct(iterable $listeners)
    {
        $this->listeners = $listeners;
    }

    public function boot(AppInterface $app): void
    {
        $handler = $app->get(ErrorHandlerInterface::class);

        if ($handler instanceof ErrorHandler)
        {
            foreach ($this->getListeners($app) as $listener)
            {
                $handler->listen($listener);
            }
        }
    }

    /**
     * @return ErrorListenerInterface[]
     */
    public function getListeners(AppInterface $app): iterable
    {
        foreach ($this->listeners as $listener)
        {
            if (!$listener instanceof ErrorListenerInterface)
            {
                $listener = $app->get($listener);
            }
            
            yield $listener;
        }
    }
}
