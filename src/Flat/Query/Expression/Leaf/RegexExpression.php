<?php

namespace Mattbit\Flat\Query\Expression\Leaf;

use Mattbit\Flat\Document\Matchable;

class RegexExpression extends Expression
{
    public function match(Matchable $document)
    {
        return (bool) preg_match($this->reference, $document->get($this->key));
    }
}
