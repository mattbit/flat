<?php

use Mattbit\Flat\Model\Document;
use Mattbit\Flat\Query\Expression\ExpressionInterface;
use Mattbit\Flat\Query\Matcher;
use Mockery as m;

class MatcherTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testMatch()
    {
        $document = new Document();
        $expression = m::mock(ExpressionInterface::class);
        $expression->shouldReceive('match')->with($document);

        $matcher = new Matcher($expression);
        $matcher->match($document);
    }
}