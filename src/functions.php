<?php


namespace Bermuda;


use Laminas\Config\Config;
use Bermuda\App\AppInterface;
use Bermuda\Registry\Registry;
use Bermuda\Router\GeneratorInterface;
use Bermuda\Templater\RendererInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;


/**
 * @param string $entry
 * @return AppInterface|mixed
 */
function app(string $entry = null)
{
    if ($entry != null)
    {
        return Registry::get(AppInterface::class)->get($entry);
    }
    
    return Registry::get(AppInterface::class);
}

/**
 * @param string $entry
 * @return mixed
 */
function get(string $entry)
{
    return Registry::get(AppInterface::class)->get($entry);
}

/**
 * @param string $entry
 * @return object
 */
function service(string $service): object
{
    return Registry::get(AppInterface::class)->get($service);
}

/**
 * @param string|int|null $key
 * @return Config|mixed
 */
function config($key = null)
{   
    return $key == null ? new Config(get('config')) : (new Config(get('config')))->{$key};
}

/**
 * @param string $template
 * @param array $params
 * @return ResponseInterface
 */
function view(string $template, array $params = []): ResponseInterface
{
    return html_response(service(RendererInterface::class)->render($template, $params));
}

/**
 * @param int $code
 * @param string $reasonPhrase
 * @return ResponseInterface
 */
function response(int $code = 200, string $reasonPhrase = ''): ResponseInterface
{
    return service(ResponseFactoryInterface::class)->createResponse($code, $reasonPhrase);
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
  
    return ($response ?? response())->withHeader('location', (string) $uri)->withStatusCode(302);
}

/**
 * @param string $name
 * @param array $params
 * @return string
 */
function route(string $name, array $params = []): string
{
    return service(GeneratorInterface::class)->generate($name, $params);
}

/**
 * @param string $name
 * @param array $params
 * @return ResponseInterface
 */
function redirect_on_route(string $name, array $params = []): ResponseInterface
{
    return redirect(route($name, $params));
}

/**
 * @param string $json
 * @param ResponseInterface|null $response
 * @return ResponseInterface
 */
function json_response(string $json, ?ResponseInterface $response = null): ResponseInterface
{
     if ($response == null)
     {
        $response = response();
     }
  
     $response->getBody()->write($json);
    
     return $response->withHeader('Content-Type', 'application/json');
}

/**
 * @param string $json
 * @param ResponseInterface|null $response
 * @return ResponseInterface
 */
function html_response(string $html, ?ResponseInterface $response = null): ResponseInterface
{
     if ($response == null)
     {
        $response = response();
     }
  
     $response->getBody()->write($html);
    
     return $response->withHeader('Content-Type', 'text/html');
}
