<?php

namespace Bermuda\App;

use Throwable;
use Bermuda\App\Exceptions\AppException;
use DI\FactoryInterface;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ErrorHandler\ErrorHandler;
use Autocomplete\App as Autocomplete;

/**
 * @mixin Autocomplete
 * @property-read ErrorHandler $errorHandler
 * @property-read Config $config
 */
interface AppInterface extends ContainerInterface, 
    FactoryInterface, InvokerInterface
{
    const devMode = 'devMode';
        
    /**
     * Run application
     */
    public function run(): void ;

    /**
     * @param mixed $any
     * @return AppInterface
     * @throws \RuntimeException
     */
    public function pipe(mixed $any): AppInterface ;
        
    /**
     * @param Throwable $e
     * @return never
     */
    public function handleException(Throwable $e): never ;
         
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
    public function set(string $id, $value): AppInterface ;
    
     /**
     * @param string $id
     * @param callable $extender
     * @return AppInterface
     * @throws AppException
     */
    public function extend(string $id, callable $extender): AppInterface ;
}
