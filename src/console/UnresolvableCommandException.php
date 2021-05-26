<?php

namespace Bermuda\App\Console;

use Throwable;

/**
 * Class UnresolvableCommandException
 * @package Bermuda\App\Console
 */
final class UnresolvableCommandException extends \RuntimeException
{
    public function __construct($message = "Unresolvable command", ?string $file = null, ?string $line = null)
    {
        !$file ?: $this->file = $file;
        !$line ?: $this->line = $line;
        parent::__construct($message);
    }
}
