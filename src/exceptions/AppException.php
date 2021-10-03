<?php

namespace Bermuda\App\Exceptions;

use Bermuda\Exceptor\Exceptor;

class AppException extends \RuntimeException
{
    use Exceptor;
    
    public static function isRun(): self
    {
        return new static('Application already run');
    }

    public static function entryExists(string $id): self
    {
        return static::create('Entry with id: %s already exists in the container', $id);
    }
    
    public static function callback(string $name): self
    {
        return static::create('Callback [%s] already registered in app', $name);
    }
}
