<?php

namespace Mattbit\Flat\Query\Expression;

use Mattbit\Flat\Document\Matchable;

interface ExpressionInterface
{
    public function match(Matchable $document);
}
