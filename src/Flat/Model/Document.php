<?php

namespace Mattbit\Flat\Model;

class Document extends \ArrayObject implements DocumentInterface
{
    /**
     * Get a document attribute using dot notation.
     *
     * @param $name
     *
     * @return mixed
     */
    public function get($name)
    {
        if (array_key_exists($name, $this)) {
            return $this[$name];
        }

        $attributes = $this->getArrayCopy();

        foreach (explode('.', $name) as $key) {
            if (is_array($attributes) && array_key_exists($key, $attributes)) {
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
        if (array_key_exists($name, $this)) {
            return true;
        }

        $attributes = $this->getArrayCopy();

        foreach (explode('.', $name) as $key) {
            if (is_array($attributes) && array_key_exists($key, $attributes)) {
                $attributes = $attributes[$key];
            } else {
                return false;
            }
        }

        return true;
    }

    public function set($name, $value)
    {
        $keys = explode('.', $name);

        $attributes = &$this;

        while (count($keys) > 1) {
            $key = array_shift($keys);
            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($attributes[$key]) || !is_array($attributes[$key])) {
                $attributes[$key] = [];
            }
            $attributes = &$attributes[$key];
        }

        $attributes[array_shift($keys)] = $value;
    }

    public function getId()
    {
        return $this->get('_id');
    }

    public function setId($id)
    {
        return $this->set('_id', $id);
    }
}
