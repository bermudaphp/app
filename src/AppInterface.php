<?php

namespace Bermuda\App;

use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ServiceFactory\FactoryInterface;
use Bermuda\ErrorHandler\ErrorHandlerInterface;

/**
 * Interface AppInterface
 * @package Bermuda\App
 */
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
     * @param mixed $id
     * @param mixed|null $default
     * @param bool $invoke
     * @return mixed
     */
    public function get($id, $default = null, bool $invoke = false)
     
    /**
     * @param string $name
     * @return string
     */
    public function name(?string $name = null): string ;
    
    /**
     * @param string $version
     * @return string
     */
    public function version(?string $version = null): string ;
        
    /**
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface ;

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
