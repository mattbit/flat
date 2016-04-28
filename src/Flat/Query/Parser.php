<?php

namespace Mattbit\Flat\Query;

use Mattbit\Flat\Query\Expression\Factory;
use Mattbit\Flat\Query\Expression\Leaf\EqExpression;
use Mattbit\Flat\Query\Expression\Tree\AndExpression;
use Mattbit\Flat\Query\Expression\Tree\Expression as TreeExpression;

class Parser
{
    protected $factory;

    public function __construct()
    {
        $this->factory = new Factory();
    }

    /**
     * Parses a query and returns an ExpressionInterface.
     * That’s the core logic of the database.
     *
     * @param $query
     *
     * @return ExpressionInterface
     */
    public function parse(array $query)
    {
        return $this->parseExpression($query);
    }

    protected function parseExpression($element, $path = null)
    {
        $expressions = [];

        // If the element is a simple value, we return an EqualityExpression.
        if (!is_array($element)) {
            return new EqExpression($path, $element);
        }

        // Otherwise we parse each nested expression. If we find an operator
        // we parse the expression, otherwise we recursively call
        // `parseExpression` with the nested path.
        foreach ($element as $key => $value) {
            if ($this->isOperator($key)) {
                $expressions[] = $this->parseOperator($key, $value, $path);
            } else {
                $nextPath = $path ? "{$path}.{$key}" : $key;
                $expressions[] = $this->parseExpression($value, $nextPath);
            }
        }

        // If we have one expression, we just return that. If there are
        // more than one, we should wrap them into a LogicAnd.
        if (count($expressions) === 1) {
            return $expressions[0];
        }

        return new AndExpression($expressions);
    }

    protected function parseOperator($operator, $value, $path)
    {
        $expression = $this->factory->make($operator, $path, $value);

        // If the expression is a TreeExpression we must parse the nested
        // elements and add them to the expression.
        if ($expression instanceof TreeExpression) {
            return $this->parseTreeExpression($expression, $value, $path);
        }

        return $expression;
    }

    protected function parseTreeExpression(TreeExpression $expression, $element, $path)
    {
        if (!is_array($element)) {
            $class = get_class($expression);
            throw new \Exception("The `{$class}` element must be an array");
        }

        foreach ($element as $value) {
            $expression->add($this->parseExpression($value, $path));
        }

        return $expression;
    }

    protected function isOperator($key)
    {
        return $key[0] === '$';
    }
}
