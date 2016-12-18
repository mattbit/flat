<?php

use Mattbit\Flat\Model\Document;
use Mattbit\Flat\Model\DocumentInterface;
use Mattbit\Flat\Query\Expression\ExpressionInterface;
use Mattbit\Flat\Query\Expression\Tree\AndExpression;
use Mattbit\Flat\Query\Expression\Tree\OrExpression;
use Mattbit\Flat\Query\Expression\Tree\NotExpression;

class TreeExpressionTest extends PHPUnit_Framework_TestCase
{
    public function testAnd()
    {
        $this->assertMatches(new AndExpression([new TrueExpression()]));
        $this->assertMatches(new AndExpression([new TrueExpression(), new TrueExpression()]));
        $this->assertNotMatches(new AndExpression([new FalseExpression(), new TrueExpression()]));
        $this->assertNotMatches(new AndExpression([new FalseExpression()]));
        $this->assertNotMatches(new AndExpression([new FalseExpression(), new FalseExpression()]));
    }

    public function testOr()
    {
        $this->assertMatches(new OrExpression([new TrueExpression()]));
        $this->assertMatches(new OrExpression([new TrueExpression(), new TrueExpression()]));
        $this->assertMatches(new OrExpression([new FalseExpression(), new TrueExpression()]));
        $this->assertNotMatches(new OrExpression([new FalseExpression(), new FalseExpression()]));
    }

    public function testNot()
    {
        $this->assertMatches(new NotExpression([new FalseExpression()]));
        $this->assertMatches(new NotExpression([new FalseExpression(), new FalseExpression()]));
        $this->assertNotMatches(new NotExpression([new TrueExpression()]));
        $this->assertNotMatches(new NotExpression([new TrueExpression(), new FalseExpression()]));
    }

    protected function assertMatches(ExpressionInterface $expression)
    {
        $this->assertTrue($expression->match(new Document()));
    }

    protected function assertNotMatches(ExpressionInterface $expression)
    {
        $this->assertFalse($expression->match(new Document()));
    }
}

class TrueExpression implements ExpressionInterface
{
    public function match(DocumentInterface $document)
    {
        return true;
    }
}

class FalseExpression implements ExpressionInterface
{
    public function match(DocumentInterface $document)
    {
        return false;
    }
}