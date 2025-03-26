<?php

namespace Bermuda\App\Console;

interface CommandResolverInterface
{
    /**
     * @throws UnresolvableCommandException
     */
    public function resolve(mixed $any): CommandInterface ;
}
