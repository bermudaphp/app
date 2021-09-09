<?php

namespace Bermuda\App;

use Throwable;

class AppException extends \RuntimeException
{
    public static function runned(): self
    {
        return new static('Application already runned.');
    }
}
