<?php

namespace Bermuda\App;

use Laminas\Config as LaminasConfig;

/**
 * Class Config
 * @package Bermuda\App
 */
final class Config implements ConfigInterface
{  
    public function __construct(private array $data)
    {
        $this->delegate = new LaminasConfig($data);
    }
  
    public function get($name, $default = null, bool $invoke = true)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name]
          : ($invoke && is_callable($default) ? $default() : $default)
    }
  
    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        throw new \RuntimeException('Config is not immutable');
    }
  
    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        $data  = $this->data;

        /** @var self $value */
        foreach ($data as $key => $value)
        {
            $array[$key] = $value instanceof self
              ? $value->toArray() : $value ;
        }

        return $array;
    }

    /**
     * isset() overloading
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    public function __unset($name)
    {
        throw new \RuntimeException('Config is not immutable');
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function current()
    {
        return current($this->data);
    }

    /**
     * key(): defined by Iterator interface.
     *
     * @see    Iterator::key()
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * next(): defined by Iterator interface.
     *
     * @see    Iterator::next()
     * @return void
     */
    public function next()
    {
        next($this->data);
    }

    public function rewind()
    {
        reset($this->data);
    }

    /**
     * valid(): defined by Iterator interface.
     *
     * @see    Iterator::valid()
     * @return bool
     */
    public function valid()
    {
        return ($this->key() !== null);
    }

    /**
     * offsetExists(): defined by ArrayAccess interface.
     *
     * @see    ArrayAccess::offsetExists()
     * @param  mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->isset($offset);
    }

    /**
     * offsetGet(): defined by ArrayAccess interface.
     *
     * @see    ArrayAccess::offsetGet()
     * @param  mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * offsetSet(): defined by ArrayAccess interface.
     *
     * @see    ArrayAccess::offsetSet()
     * @param  mixed $offset
     * @param  mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->_set($offset, $value);
    }

    /**
     * offsetUnset(): defined by ArrayAccess interface.
     *
     * @see    ArrayAccess::offsetUnset()
     * @param  mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }
}
