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
     * @throws \RuntimeException
     */
    public function resolve($any): CommandInterface ;
}
