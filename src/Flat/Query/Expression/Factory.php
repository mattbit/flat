<?php

namespace Mattbit\Flat\Query\Expression;

use Mattbit\Flat\Query\Expression\Leaf\EqExpression;
use Mattbit\Flat\Query\Expression\Leaf\Expression;
use Mattbit\Flat\Query\Expression\Leaf\InExpression;
use Mattbit\Flat\Query\Expression\Tree\NotExpression;

class Factory
{
    private $namespace = 'Mattbit\Flat\Query\Expression';

    /**
     * Return an Expression based on the operator.
     *
     * @param $operator
     * @param $key
     * @param $reference
     * @return ExpressionInterface
     * @throws \Exception
     */
    public function make($operator, $key = null, $reference = null)
    {
        switch ($operator) {
            // Leaf expressions
            case Type::EQ:
            case Type::GT:
            case Type::GTE:
            case Type::LT:
            case Type::LTE:
            case Type::IN:
            case Type::REGEX:
                $class = $this->classFromOperator('Leaf', $operator);

                return new $class($key, $reference);

            // Tree expressions
            case Type::AND_MATCH:
            case Type::OR_MATCH:
            case Type::NOT:
                $class = $this->classFromOperator('Tree', $operator);

                return new $class();

            // Negations
            case Type::NE:
                return new NotExpression([new EqExpression($key, $reference)]);
            case Type::NIN:
                return new NotExpression([new InExpression($key, $reference)]);

            default:
                throw new \Exception("Unknown operator `$operator`.");
        }
    }

    protected function classFromOperator($prefix, $operator)
    {
        return sprintf(
            '%s\%s\%sExpression',
            $this->namespace,
            $prefix,
            ucfirst(ltrim($operator, '$'))
        );
    }
}
