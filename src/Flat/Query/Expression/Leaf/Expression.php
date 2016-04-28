<?php

namespace Mattbit\Flat\Query\Expression\Leaf;

class Expression
{
    protected $key;

    protected $reference;

    public function __construct($key, $reference)
    {
        $this->key = $key;
        $this->reference = $reference;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getReference()
    {
        return $this->reference;
    }
}
