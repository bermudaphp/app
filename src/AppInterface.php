<?php

namespace Bermuda\App;

use Bermuda\App\Exceptions\AppException;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ServiceFactory\FactoryInterface;
use Bermuda\ErrorHandler\ErrorHandlerInterface;
use Autocomplete\App as Autocomplete;

/**
 * @mixin Autocomplete
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
     * @return AppInterface
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
    public function registerCallback(string $name, callable $callback): AppInterface ;

    /**
     * @param string $alias
     * @param string $link
     * @return AppInterface
     * @throws AppException
     */
    public function registerAlias(string $alias, string $link): AppInterface ;

    /**
     * @param $name
     * @return mixed
     * @see ContainerInterface::get()
     */
    public function __get(string $name);

    /**
     * @return Config
     */
    public function getConfig(): Config ;

    /**
     * @param string $id
     * @param $value
     * @return AppInterface
     * @throws AppException
     */
    public function set(string $id, $value): self ;
    
     /**
     * @param string $id
     * @param callable $extender
     * @return AppInterface
     * @throws AppException
     */
    public function extend(string $id, callable $extender): self ;
}
