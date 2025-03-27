<?php

namespace Bermuda\App\Compile;

use Bermuda\App\AppInterface;
use Bermuda\App\Trait\ConsoleApp;
use DI\CompiledContainer;

class CompiledConsole extends CompiledContainer implements AppInterface
{
    use ConsoleApp;
}
