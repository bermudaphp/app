<?php

namespace Bermuda\App;

use Bermuda\Registry\Registry;
use Bermuda\ServiceFactory\FactoryException;

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
 * @return bool
 */
function is_console_sapi(): bool
{
    return PHP_SAPI == 'cli';
}
