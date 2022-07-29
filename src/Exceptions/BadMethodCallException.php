<?php

namespace Bermuda\App\Exceptions;

use Bermuda\Exceptor\Exceptor;

class BadMethodCallException extends \BadMethodCallException
{
    use Exceptor;
}
