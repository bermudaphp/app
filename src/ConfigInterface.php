<?php

namespace Bermuda\App;

use Bermuda\Arrayable

/**
 * Interface Config
 */
Interface Config implements Countable, Iterator, ArrayAccess, Arrayable
{
    public function get($name, $default = null, bool $invoke = true);

    public function __get($name);

    public function __set($name, $value);

    public function __isset($name): bool ;

    public function __unset($name): void ;

    public function count(): int ;
}
