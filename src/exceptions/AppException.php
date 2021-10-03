<?php

namespace Bermuda\App\Exceptions;

use Bermuda\Exceptor\Exceptor;

class AppException extends \RuntimeException
{
    use Exceptor;
    
    public static function isRun(): self
    {
        return new static('Application is already run');
    }

    public static function entryExists(string $id): self
    {
        return static::create('Entry with id: %s is already exists in the container', $id);
    }

    public static function aliasExists(string $id): self
    {
        return static::create('Alias: %s is already registered in the app', $id);
    }
    
    public static function callback(string $name): self
    {
        return static::create('Callback [%s] is already registered in the app', $name);
    }
}
