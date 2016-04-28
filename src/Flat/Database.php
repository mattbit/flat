<?php

namespace Mattbit\Flat;

class Database
{
    /**
     * An array of the registered collections.
     *
     * @var array
     */
    protected $collections;

    /**
     * Select an existing collection or create a new one.
     *
     * @param string $name
     *
     * @return Collection
     */
    public function collection($name)
    {
        return new Collection($name);
    }
}
