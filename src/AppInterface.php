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
    const version = '1.0.3';

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
     * @param string $id
     * @param $value
     * @return $this
     * @throws \RuntimeException
     */
    public function set(string $id, $value): self ;
}
