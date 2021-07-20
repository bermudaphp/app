<?php

namespace Bermuda\App;

/**
 * Class AppException
 * @package Bermuda\App
 */
class AppException extends \RuntimeException
{
    public static function alreadyBooted(): self
    {
        return new static('Application already booted.');
    }
  
    public static function alreadyRunned(): self
    {
        return new static('Application already runned.');
    }
  
    public static function fromPrev(\Throwable $e): self
    {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}
