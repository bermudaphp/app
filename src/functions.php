<?php

namespace Bermuda\App;

use Bermuda\Registry\Registry;
use Bermuda\Router\Router;
use Bermuda\ServiceFactory\FactoryException;
use Bermuda\ServiceFactory\FactoryInterface;
use Bermuda\String\Json;
use Bermuda\Templater\RendererInterface;
use League\Flysystem\FilesystemException;
use Bermuda\Utils\{Header, URL, Types\Text, Types\Application};
use Bermuda\Flysystem\{Location, Image, Flysystem, Exceptions\NoSuchFile};
use Psr\Http\Message\{ResponseFactoryInterface, ResponseInterface, UriInterface};
use function Bermuda\ErrorHandler\get_error_code;

/**
 * @param string|null $entry
 * @param null $default
 * @return mixed|null|AppInterface
 */
function app(string $entry = null, $default = null)
{
    if ($entry != null) {
        return ($app = Registry::get(AppInterface::class))->has($entry) ?
            $app->get($entry) : $default;
    }

    return Registry::get(AppInterface::class);
}

/**
 * @param string $entry
 * @param null $default
 * @return AppInterface|mixed|string|null
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
    return app()->make($cls, $params);
}

/**
 * @param string|int|null $key
 * @return Config|mixed
 */
function config(string|int|null $key = null)
{
    if ($key !== null) {
        return app()->getConfig()[$key];
    }
    
    return app()->getConfig();
}

/**
 * @param string $template
 * @param array $params
 * @return string
 */
function render(string $template, array $params = []): string
{
    return app()->render($template, $params);
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

    if ($template === null) {
        $template = sprintf('errors::%s', $code);
    }

    return html(render($template), $response);
}

function path(?string $location = null): Location
{
    if ($location !== null) {
        return (new Location(\getcwd()))->append($location);
    }
    
    return new Location(\getcwd());
}

/**
 * @param string $location
 * @param bool $inline
 * @return ResponseInterface
 * @throws FilesystemException
 * @throws NoSuchFile
 */
function file(string $location, ?bool $inline = null): ResponseInterface
{
    $file = service(Flysystem::class)->openFile($location);
    return $file->respond(response(), $inline ?? $file instanceof Image);
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
function respond(int $code = 200, string $content): ResponseInterface
{
    return service(ResponseFactoryInterface::class)->createResponse($code, $reasonPhrase);
}

function write(ResponseInterface $response, string $content, array $headers = [], int &$size = null): ResponseInterface
{
    foreach ($headers as $name => $value) {
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
    return ($response ?? response())->withHeader(Header::location, (string)$uri)->withStatus(302);
}

/**
 * @param string $routeName
 * @param array $params
 * @return string
 */
function route(string $routeName, array $params = [], bool $asUrl = false): string
{
    $path = service(Route::class)->generate($routeName, $params);
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
