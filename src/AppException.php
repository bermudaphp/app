<?php

namespace Bermuda\App;

use RuntimeException;

class AppException extends RuntimeException
{
    public static function isRun(): self
    {
        return new static('Application already run');
    }

    public static function entryExists($id): self
    {
        return new static(sprintf('Entry with id: %s already exists in the container', $id));
    }
}
