<?php

namespace Bermuda\App\Compile;

use Bermuda\App\AppInterface;
use Bermuda\App\Trait\ServerApp;
use DI\CompiledContainer;
use Psr\Http\Server\RequestHandlerInterface;

class CompiledServer extends CompiledContainer implements AppInterface, RequestHandlerInterface
{
    use ServerApp;
}
