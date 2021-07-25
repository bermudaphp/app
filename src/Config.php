<?php

namespace Bermuda\App;

/**
 * Class Config
 * @package Bermuda\App
 */
final class Config implements ConfigInterface
{  
    private array $data;
    
    public function __construct(iterable $data)
    {
        $array = [];
        
        foreach($data as $k => $v)
        {
            $array[$k] = is_iterable($v) ? new Config($v) : $v ;
        }
        
        $this->data = $array ;
    }
  
    public function get($name, $default = null, bool $invoke = true)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name]
          : ($invoke && is_callable($default) ? $default() : $default)
    }
  
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }
    
    public function toArray(): array
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

    public function __isset($name): bool
    {
        return $this->offsetExists($name);
    }

    public function __unset($name): void
    {
        $this->offsetUnset($name);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function current()
    {
        return current($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

    public function next()
    {
        next($this->data);
    }

    public function rewind()
    {
        reset($this->data);
    }

    public function valid()
    {
        return ($this->key() !== null);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($name, $this->data);
    }

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
         throw new \RuntimeException('Config is not immutable');
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
         throw new \RuntimeException('Config is not immutable');
    }
}
