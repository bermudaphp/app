<?php

namespace Bermuda\App;

use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ServiceFactory\FactoryInterface;

/**
 * Interface AppInterface
 * @package Bermuda\App
 */
interface AppInterface extends ContainerInterface, FactoryInterface, InvokerInterface
{
    /**
     * Run application
     */
    public function run(): void ;

    /**
     * @param mixed $any
     * @return $this
     */
    public function pipe($any): self ;
    
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
     * @param string $id
     * @param $value
     * @return $this
     * @throws \RuntimeException
     */
    public function set(string $id, $value): self ;
    
     /**
     * @param string $id
     * @param callable $extender
     * @return $this
     * @throws \RuntimeException
     */
    public function extend(string $id, callable $extender): self ;
}
