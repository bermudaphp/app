<?php

namespace Bermuda\App;

use Bermuda\App\Boot\Bootable;
use Bermuda\App\Boot\BootstrapperInterface;
use Throwable;
use Bermuda\App\Exceptions\AppException;
use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ErrorHandler\ErrorHandler;
use Autocomplete\App as Autocomplete;
use Bermuda\Config\Config;

/**
 * @mixin Autocomplete
 */
interface AppInterface extends ContainerInterface, 
    FactoryInterface, InvokerInterface
{
    public Config $config { get ;}
    public ErrorHandler $errorHandler { get ;}

    /**
     * Run application
     * @throws AppException
     * If application is already runned
     */
    public function run(?Bootable $bootable = null): void ;

    /**
     * @param mixed $any
     * @return AppInterface
     * @throws AppException
     */
    public function pipe(mixed $any): AppInterface ;

    /**
     * @param string $name
     * @param array $arguments
     * @return \BadMethodCallException
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
     * @throws AppException
     */
    public function set(string $id, mixed $value): void ;
    
     /**
     * @throws AppException
     */
    public function extend(string $id, callable $extender): AppInterface ;
}
