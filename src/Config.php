<?php

namespace Bermuda\App;

use Bermuda\Arrayable;
use Psr\Container\{
    ContainerInterface, 
    NotFoundExceptionInterface, 
    ContainerExceptionInterface
};
use Countable, Iterator, ArrayAccess, RuntimeException;

final class Config implements Countable, Iterator, ArrayAccess, Arrayable
{
    private array $data = [];

    public function __construct(iterable $data)
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = is_iterable($value)
                ? new Config($value) : $value;
        }
    }

    /**
     * @param ContainerInterface $container
     * @return static
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     */
    public static function makeFrom(ContainerInterface $container): self
    {
        return new self($container->get('config'));
    }

    /**
     * @param $name
     * @return mixed|self
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * @throws RuntimeException
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * @inerhitDoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param $offset
     * @param null $default
     * @param bool $invoke
     * @return self|null|mixed
     */
    public function get($offset, $default = null, bool $invoke = true)
    {
        return $this->offsetExists($offset) ? $this->data[$offset]
            : ($invoke && is_callable($default) ? $default() : $default);
    }

    /**
     * @throws RuntimeException
     */
    public function offsetSet($offset, $value)
    {
        throw $this->notImmutable();
    }

    /**
     * @inerhitDoc
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->data as $key => $value) {
            $array[$key] = $value instanceof Config
                ? $value->toArray() : $value;
        }

        return $array;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return $this->offsetExists($name);
    }

    /**
     * @inerhitDoc
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * @throws RuntimeException
     */
    public function __unset($name): void
    {
        $this->offsetUnset($name);
    }

    /**
     * @throws RuntimeException
     */
    public function offsetUnset($offset)
    {
        throw $this->notImmutable();
    }

    /**
     * @inerhitDoc
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @inerhitDoc
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * @inerhitDoc
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * @inerhitDoc
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * @inerhitDoc
     */
    public function valid(): bool
    {
        return ($this->key() !== null);
    }

    /**
     * @inerhitDoc
     */
    public function key(): float|bool|int|string|null
    {
        return key($this->data);
    }

    private function notImmutable(): RuntimeException
    {
        return new RuntimeException('Config is not immutable');
    }
}
