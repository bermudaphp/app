<?php

namespace Bermuda;

use Bermuda\Flysystem\Exceptions\NoSuchFile;
use Bermuda\Flysystem\Flysystem;
use Bermuda\Flysystem\Image;
use Bermuda\String\Json;
use Bermuda\Utils\Types\Application;
use Bermuda\Utils\Header;
use Bermuda\Utils\Types\Text;
use Bermuda\Utils\URL;
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
use function Bermuda\ErrorHandler\get_error_code;
use function Bermuda\ErrorHandler\get_status_code_from_throwable;

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
 * @return string
 */
function render(string $template, array $params = []): string
{
    return service(RendererInterface::class)->render($template, $params);
}

/**
 * @param string $template
 * @param array $params
 * @return ResponseInterface
 */
function view(string $template, array $params = []): ResponseInterface
{
    return html(render($template, $params));
}

function err(int $code, ?string $template = null): ResponseInterface
{
    $code = get_error_code($code);
    $response = response($code);

    if ($template === null)
    {
        $template = sprintf('errors::%s', $code);
    }

    return html(render($template), $response);
}

/**
 * @param string $location
 * @param bool $inline
 * @return ResponseInterface
 * @throws \League\Flysystem\FilesystemException
 * @throws NoSuchFile
 */
function file(string $location, ?bool $inline = null): ResponseInterface
{
    $file = service(Flysystem::class)->openFile($location);
    return $file->responde(response(), $inline ?? $file instanceof Image);
}

/**
 * @param array $segments
 * @return string
 */
function build_url(array $segments = []): string
{
    return URL::build($segments);
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

function write(ResponseInterface $response, string $content, array $headers = [], int &$size = null): ResponseInterface
{    
    foreach($headers as $name => $value)
    {
        $response = $response->withHeader($name, $value);
    }
    
    $size = $response->getBody()->write($content);
    
    return $response;
}

function onRoute(string $routeName, array $params = []): ResponseInterface
{
    return redirect(route($routeName, $params));
}

/**
 * @param string|UriInterface $uri
 * @param ResponseInterface|null $response
 * @return ResponseInterface
 */
function redirect(string|UriInterface $uri = '/', ?ResponseInterface $response = null): ResponseInterface
{
    return ($response ?? response())->withHeader(Header::location, (string) $uri)->withStatus(302);
}

/**
 * @param string $routeName
 * @param array $params
 * @return string
 */
function route(string $routeName, array $params = [], bool $asUrl = false): string
{
    $path = service(GeneratorInterface::class)->generate($routeName, $params);
    return $asUrl ? Url::build(compact('path')) : $path;
}

function json($content, ?ResponseInterface $response = null): ResponseInterface
{
    return write($response ?? response(),
        Json::isJson($content) ? $content : Json::encode($content),
        [Header::contentType => Application::json]
    );
}

function html(string $content, ?ResponseInterface $response = null): ResponseInterface
{
     return write($response ?? response(), $content, [Header::contentType => Text::html]);
}

function is_console_sapi(): bool
{
    return PHP_SAPI == 'cli';
}
