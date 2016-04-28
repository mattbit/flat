<?php

namespace Mattbit\Flat\Query\Expression\Tree;

use Mattbit\Flat\Document\Matchable;

class AndExpression extends Expression
{
    public function match(Matchable $document)
    {
        foreach ($this->expressions as $expression) {
            if (!$expression->match($document)) {
                return false;
            }
        }

        return true;
    }
}
