<?php

namespace Mattbit\Flat\Query\Expression;

use Mattbit\Flat\Model\DocumentInterface;

interface ExpressionInterface
{
    public function match(DocumentInterface $document);
}
