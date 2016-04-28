<?php

namespace Mattbit\Flat\Query\Expression\Leaf;

use Mattbit\Flat\Document\Matchable;

class InExpression extends Expression
{
    public function match(Matchable $document)
    {
        return in_array($document->get($this->key), $this->reference);
    }
}
