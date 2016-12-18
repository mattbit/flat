<?php

namespace Mattbit\Flat\Query;

use Mattbit\Flat\Model\DocumentInterface;
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
        // @todo: the expression should be parsed here
        $this->expression = $expression;
    }

    /**
     * Check if a document matches the expression.
     *
     * @param DocumentInterface $document
     * @return mixed
     */
    public function match(DocumentInterface $document)
    {
        return $this->expression->match($document);
    }
}
