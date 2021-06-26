<?php

namespace Bermuda;

use Bermuda\String\Json;
use Laminas\Config\Config;
use Bermuda\App\AppInterface;
use Bermuda\Registry\Registry;
use Bermuda\Router\GeneratorInterface;
use Bermuda\Templater\RendererInterface;
use Bermuda\ServiceFactory\FactoryInterface;
use Bermuda\ServiceFactory\FactoryException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @param string $entry
 * @param $default
 * @return AppInterface|mixed|null
 */
function app(string $entry = null, $default = null)
{
    if ($entry != null)
    {
        return ($app = Registry::get(AppInterface::class))->has($entry) ? 
            Registry::get(AppInterface::class)->get($entry) : $default;
    }
    
    return Registry::get(AppInterface::class);
}

/**
 * @param string $entry
 * @param $default
 * @return mixed
 */
function get(string $entry, $default = null)
{
    return app($entry, $default);
}

/**
 * @param string $service
 * @return object
 */
function service(string $service): object
{
    return app($service);
}

/**
 * @param string $cls
 * @param array $params
 * @return object
 * @throws FactoryException
 */
function make(string $cls, array $params = []): object
{
    return app(FactoryInterface::class)->make($cls, $params);
}

/**
 * @param string|int|null $key
 * @return Config|mixed
 */
function config($key = null)
{   
    return $key == null ? new Config(app('config')) : (new Config(app('config')))->{$key};
}

/**
 * @param string $template
 * @param array $params
 * @return ResponseInterface
 */
function view(string $template, array $params = []): ResponseInterface
{
    return html(service(RendererInterface::class)->render($template, $params));
}

/**
 * @param int $code
 * @param string $reasonPhrase
 * @return ResponseInterface
 */
function r(int $code = 200, string $reasonPhrase = ''): ResponseInterface
{
    return service(ResponseFactoryInterface::class)->createResponse($code, $reasonPhrase);
}

function r_write(ResponseInterface $r, string $content, array $headers = [], int &$size = null): ResponseInterface
{    
    foreach($headers as $name => $value)
    {
        $r = $r->withHeader($name, $value);
    }
    
    $size = $r->getBody()->write($content);
    
    return $r;
}

/**
 * @param string|UriInterface $uri
 * @param ResponseInterface|null $response
 * @return ResponseInterface
 */
function redirect($uri = '/', ?ResponseInterface $response = null): ResponseInterface
{
    if (!is_string($uri) && !$uri instanceof UriInterface)
    {
        throw new \InvalidArgumentException(sprintf('Uri provided to %s must be a string or %s instance; received "%s"', __FUNCTION__, UriInterface::class, (is_object($uri) ? get_class($uri) : gettype($uri))));
    }
  
    return ($response ?? r())->withHeader('location', (string) $uri)->withStatus(302);
}

/**
 * Генерирует url для маршрута 
 * с именем $routeName
 * @param string $routeName
 * @param array $params
 * @return string
 */
function urlFor(string $routeName, array $params = []): string
{
    return service(GeneratorInterface::class)->generate($routeName, $params);
}

/**
 * Перенаправляет на url связанный 
 * с маршрутом с именем $routeName
 * @param string $name
 * @param array $params
 * @return ResponseInterface
 */
function reTo(string $routeName, array $params = []): ResponseInterface
{
    return redirect(urlFor($routeName, $params));
}

function json($content, ?ResponseInterface $response = null): ResponseInterface
{
    return r_write($response ?? r(), Json::isJson($content) ? $content : Json::encode($content), ['Content-Type' => 'application/json']);
}

function html(string $content, ?ResponseInterface $response = null): ResponseInterface
{
     return r_write($response ?? r(), $content, ['Content-Type' => 'text/html']);
}

function is_console_sapi(): bool
{
    return PHP_SAPI == 'cli';
}
