<?php

namespace Mattbit\Flat\Query;

use Mattbit\Flat\Document\Matchable;
use Mattbit\Flat\Query\Expression\ExpressionInterface;

class Matcher
{
    /**
     * @var ExpressionInterface
     */
    protected $expression;

    /**
     * Construct a Matcher instance.
     *
     * @param ExpressionInterface $expression
     */
    public function __construct(ExpressionInterface $expression)
    {
        $this->expression = $expression;
    }

    /**
     * Check if a document matches the expression.
     *
     * @param Matchable $document
     * @return mixed
     */
    public function match(Matchable $document)
    {
        return $this->expression->match($document);
    }
}
