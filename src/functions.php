<?php

namespace Bermuda\App;

/**
 * @return bool
 */
function is_cli(): bool
{
    return PHP_SAPI == 'cli';
}
