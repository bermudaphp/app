<?php

namespace Bermuda\App;

use DI\Container;
use DI\Definition\Source\MutableDefinitionSource;
use DI\FactoryInterface;
use DI\Proxy\ProxyFactory;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\ErrorHandler\ErrorHandlerInterface;
use Symfony\Component\Console\Input\{ArgvInput, InputInterface};
use Symfony\Component\Console\Output\{ConsoleOutput, OutputInterface};
use Throwable;

class ConsoleApp extends Container implements AppInterface
{
    use Trait\ConsoleApp;
}
