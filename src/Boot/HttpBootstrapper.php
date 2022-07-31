<?php

namespace Bermuda\App\Boot;

use Bermuda\HTTP\Responder;
use Bermuda\App\AppInterface;
use Bermuda\Router\Generator;
use Bermuda\Router\Router;
use Bermuda\String\Json;
use Bermuda\String\Stringable;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

final class HttpBootstrapper implements BootstrapperInterface
{
    /**
     * @inerhitDoc
     */
    public function boot(AppInterface $app): AppInterface
    {
        $app->registerCallback('respond', static function(int $code = 200, $content = null, string $contentType = null) use ($app): ResponseInterface {
            return $app->get(Responder::class)->respond($code, $content, $contentType);
        });
        
        $app->registerCallback('redirect', static function(string|UriInterface $location, int $code = 302) use ($app): ResponseInterface {
            return $app->get(Responder::class)->redirect($location, $code);
        });

        $app->registerCallback('route', static function(string $name, array $params = [], bool $redirect = false) use ($app): string|ResponseInterface {
            $path = $app->router->generate($name, $params);
            return $redirect ? $app->redirect($path) : $path;
        });
        
        return $app;
    }
}
