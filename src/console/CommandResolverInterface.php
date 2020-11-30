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
     */
    public function resolve($any): CommandInterface ;
}
