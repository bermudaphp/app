<?php

namespace Bermuda\App\Console;

/**
 * Interface CommandResolverInterface
 * @package Bermuda\App\Console
 */
interface CommandResolverInterface
{
    /**
     * @param mixed $any
     * @return CommandInterface
     * @throws UnresolvableCommandException
     */
    public function resolve($any): CommandInterface ;
}
