<?php

namespace Mattbit\Flat\Query\Expression\Tree;

use Mattbit\Flat\Document\Matchable;
use Mattbit\Flat\Query\Expression\ExpressionInterface;

abstract class Expression implements ExpressionInterface
{
    protected $expressions = [];

    abstract public function match(Matchable $document);

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
