<?php

namespace Mattbit\Flat\Query\Expression\Leaf;

use DateTime;
use Mattbit\Flat\Model\Date;
use Mattbit\Flat\Model\DocumentInterface;
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

    abstract public function match(DocumentInterface $document);

    public function getKey()
    {
        return $this->key;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getValue(DocumentInterface $document)
    {
        $value = $document->get($this->key);

        if ($this->reference instanceof DateTime) {
            return new DateTime($value);
        }

        return $value;
    }
}
