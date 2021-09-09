<?php

namespace Bermuda\App\Console;

use RuntimeException;

final class UnresolvableCommandException extends RuntimeException
{
    private mixed $command;

    public function __construct(?string $message = null, $command = null)
    {
        $this->command = $command;

        if (!$message && is_string($command)) {
            $message = 'Unresolvable command: ' . $command;
        }

        parent::__construct($message ?? 'Unresolvable command');
    }

    public static function reThrow(UnresolvableCommandException $e, array $backtrace): void
    {
        $self = new self($e->getMessage(), $e->getCommand());

        $self->file = $backtrace['file'];
        $self->line = $backtrace['line'];

        throw $self;
    }

    public function getCommand()
    {
        return $this->command;
    }
}
