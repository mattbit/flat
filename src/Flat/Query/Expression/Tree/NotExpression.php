<?php

namespace Mattbit\Flat\Query\Expression\Tree;

use Mattbit\Flat\Model\DocumentInterface;

class NotExpression extends Expression
{
    public function match(DocumentInterface $document)
    {
        foreach ($this->expressions as $expression) {
            if ($expression->match($document)) {
                return false;
            }
        }

        return true;
    }
}
