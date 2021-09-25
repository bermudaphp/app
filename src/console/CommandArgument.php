<?php

namespace Bermuda\App\Console;

/**
 * @property-read string $name
 * @property-read bool $isRequired
 */
final class CommandArgument
{
    public function __construct(private string $name, private bool $isRequired = true)
    {
    }

    /**
     * @param string $name
     * @return bool|string|null
     */
    public function __get(string $name)
    {
        return match ($name){
            'name' => $this->name,
            'isRequired' => $this->isRequired,
            'default' => null
        };
    }

    /**
     * @param string $name
     * @return static
     */
    public static function required(string $name): self
    {
        return new self($name, true);
    }

    /**
     * @param string $name
     * @return static
     */
    public static function optional(string $name): self
    {
        return new self($name, false);
    }
}
