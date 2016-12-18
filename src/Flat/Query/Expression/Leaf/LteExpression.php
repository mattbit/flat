<?php

namespace Mattbit\Flat\Query\Expression\Leaf;

use Mattbit\Flat\Model\DocumentInterface;

class LteExpression extends Expression
{
    public function match(DocumentInterface $document)
    {
        return $this->getValue($document) <= $this->reference;
    }
}
