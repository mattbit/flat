<?php

namespace Mattbit\Flat\Query\Expression\Leaf;

use Mattbit\Flat\Model\DocumentInterface;

class RegexExpression extends Expression
{
    public function match(DocumentInterface $document)
    {
        return (bool) preg_match((string) $this->reference, (string) $document->get($this->key));
    }
}
