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
        if (array_key_exists($name, $this->attributes)) {
            return true;
        }

        $attributes = $this->attributes;

        foreach (explode('.', $name) as $key) {
            if (is_array($attributes) && array_key_exists($key, $attributes)) {
                $attributes = $attributes[$key];
            } else {
                return false;
            }
        }

        return true;
    }
}
