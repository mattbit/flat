<?php

namespace Mattbit\Flat\Query\Expression\Leaf;

use Mattbit\Flat\Model\DocumentInterface;

class InExpression extends Expression
{
    public function match(DocumentInterface $document)
    {
        return in_array($this->getValue($document), $this->reference);
    }
}
