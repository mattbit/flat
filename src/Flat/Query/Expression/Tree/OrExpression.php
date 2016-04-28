<?php

namespace Mattbit\Flat\Query\Expression\Tree;

use Mattbit\Flat\Document\Matchable;

class OrExpression extends Expression
{
    public function match(Matchable $document)
    {
        foreach ($this->expressions as $expression) {
            if ($expression->match($document)) {
                return true;
            }
        }

        return false;
    }
}
