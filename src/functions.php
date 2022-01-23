<?php

namespace Bermuda\App;

/**
 * @return bool
 */
function is_console_sapi(): bool
{
    return PHP_SAPI == 'cli';
}
