<?php

namespace Mattbit\Flat\Query\Expression\Leaf;

use Mattbit\Flat\Document\Matchable;
use Mattbit\Flat\Query\Expression\ExpressionInterface;

abstract class Expression implements ExpressionInterface
{
    protected $key;

    protected $reference;

    public function __construct($key, $reference)
    {
        $this->key = $key;
        $this->reference = $reference;
    }

    abstract public function match(Matchable $document);

    public function getKey()
    {
        return $this->key;
    }

    public function getReference()
    {
        return $this->reference;
    }
}
