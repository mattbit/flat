<?php

namespace Mattbit\Flat\Query;

use Mattbit\Flat\Document\Matchable;

class Matcher
{
    protected $expression;

    public function __construct($query)
    {
        $parser = new Parser();

        $this->expression = $parser->parse($query);
    }

    public function match(Matchable $document)
    {
        return $this->expression->match($document);
    }
}
