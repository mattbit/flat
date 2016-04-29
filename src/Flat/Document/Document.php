<?php

namespace Mattbit\Flat\Document;

class Document implements Matchable, Identifiable, Encodable
{
    protected $attributes;

    /**
     * Construct a document.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function getId()
    {
        return $this->get('_id');
    }

    public function set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Get a document attribute using dot notation.
     *
     * @param $name
     *
     * @return mixed
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        $attributes = $this->attributes;

        foreach (explode('.', $name) as $key) {
            if (array_key_exists($key, $attributes) && is_array($attributes[$key])) {
                $attributes = $attributes[$key];
            } else {
                return;
            }
        }

        return $attributes;
    }

    /**
     * Check if the document as a given attribute.
     *
     * @param  $name
     *
     * @return bool
     */
    public function has($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return true;
        }

        $attributes = $this->attributes;

        foreach (explode('.', $name) as $key) {
            if (array_key_exists($key, $attributes) && is_array($attributes[$key])) {
                $attributes = $attributes[$key];
            } else {
                return false;
            }
        }

        return true;
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->attributes);
    }

    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}
