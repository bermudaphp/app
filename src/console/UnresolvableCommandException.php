<?php

namespace Bermuda\App\Console;

use Throwable;

/**
 * Class UnresolvableCommandException
 * @package Bermuda\App\Console
 */
final class UnresolvableCommandException extends \RuntimeException
{
    private $command;
    
    public function __construct(?string $message = null, $command = null)
    {
        $this->command = $command;
        
        if (!$message && is_string($command))
        {
            $message = 'Unresolvable command: ' . $command;
        }

        parent::__construct($message ?? 'Unresolvable command');
    }
    
    public function getCommand()
    {
        return $this->command;
    }
    
    public static function reThrow(UnresolvableCommandException $e, array $backtrace): void
    {
        $self = new self($e->getMessage(), $e->getCommand());
        
        $self->file = $backtrace['file'];
        $self->line = $backtrace['line'];
        
        throw $self;
    }
}
