<?php

namespace Bermuda\App;

use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ServiceFactory\FactoryInterface;
use Bermuda\ErrorHandler\ErrorHandlerInterface;

interface AppInterface extends ContainerInterface, 
    FactoryInterface, InvokerInterface, ErrorHandlerInterface
{
    /**
     * Run application
     */
    public function run(): void ;

    /**
     * @param mixed $any
     * @return $this
     * @throws \RuntimeException
     */
    public function pipe($any): self ;
         
    /**
     * @param string $name
     * @param array $arguments
     * @return Exceptions\BadMethodCallException
     */
    public function __call(string $name, array $arguments): mixed ;

    /**
     * @param string $name
     * @param callable $callback
     * @return AppInterface
     * @throws AppException
     */
    public function registerCallback(string $name, callable $callback): AppInterface;
        
    /**
     * @return Config
     */
    public function getConfig(): Config ;

    /**
     * @param string $id
     * @param $value
     * @return $this
     * @throws AppException
     */
    public function set(string $id, $value): self ;
    
     /**
     * @param string $id
     * @param callable $extender
     * @return $this
     * @throws AppException
     */
    public function extend(string $id, callable $extender): self ;
}
