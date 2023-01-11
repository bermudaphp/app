<?php

namespace Bermuda\App\Compile;

class CompiledConsole extends Console
{
    /**
     * This const is overridden in child classes (compiled containers).
     * @var array
     */
    const METHOD_MAPPING = [];
    
    use CompiledTrait {
        get as compiledGet;
        has as compiledHas;
    }

    public function get($name)
    {
        if (isset($this->aliases[$name])) {
            $name = $this->aliases[$name];
        }

        return $this->compiledGet($name);
    }

    /**
     * @inerhitDoc
     */
    public function has($name): bool
    {
        return $this->compiledHas($name) || isset($this->aliases[$name]);
    }
}
