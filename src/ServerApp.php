<?php

namespace Bermuda\App;

use DI\Container;
use DI\Definition\Source\MutableDefinitionSource;
use DI\FactoryInterface;
use DI\Proxy\ProxyFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Invoker\InvokerInterface;
use Psr\Container\ContainerInterface;
use Bermuda\Pipeline\PipelineInterface;
use Bermuda\ErrorHandler\ErrorHandlerInterface;
use Bermuda\MiddlewareFactory\{
    MiddlewareFactoryInterface, 
    UnresolvableMiddlewareException
};
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Nyholm\Psr7Server\ServerRequestCreatorInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ServerApp extends Container implements AppInterface, RequestHandlerInterface
{
    use Trait\ServerApp;
}
