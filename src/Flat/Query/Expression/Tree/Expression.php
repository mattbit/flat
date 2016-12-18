<?php

namespace Mattbit\Flat\Query\Expression\Tree;

use Mattbit\Flat\Model\DocumentInterface;
use Mattbit\Flat\Query\Expression\ExpressionInterface;

abstract class Expression implements ExpressionInterface
{
    protected $expressions = [];

    abstract public function match(DocumentInterface $document);

    public function __construct($expressions = [])
    {
        $this->expressions = $expressions;
    }

    public function add($expression)
    {
        $this->expressions[] = $expression;
    }

    public function getExpressions()
    {
        return $this->expressions;
    }
}
