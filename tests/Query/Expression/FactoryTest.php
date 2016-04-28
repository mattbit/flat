<?php

use Mattbit\Flat\Query\Expression\Type;
use Mattbit\Flat\Query\Expression\Factory;
use Mattbit\Flat\Query\Expression\Leaf\EqExpression;
use Mattbit\Flat\Query\Expression\Leaf\InExpression;
use Mattbit\Flat\Query\Expression\Tree\NotExpression;
use Mattbit\Flat\Query\Expression\Tree\AndExpression;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new Factory();
    }

    public function testMakeLeafExpression()
    {
        $expression = $this->factory->make(Type::EQ, 'key', 'ref');

        $this->assertInstanceOf(EqExpression::class, $expression);
        $this->assertEquals('key', $expression->getKey());
        $this->assertEquals('ref', $expression->getReference());
    }

    public function testMakeTreeExpression()
    {
        $expression = $this->factory->make(Type::AND_MATCH, 'key', 'ref');

        $this->assertInstanceOf(AndExpression::class, $expression);
        $this->assertEmpty($expression->getExpressions());
    }

    public function testMakeNeExpression()
    {
        $expression = $this->factory->make(Type::NE, 'key', 'ref');

        $this->assertInstanceOf(NotExpression::class, $expression);
        $this->assertCount(1, $expression->getExpressions());

        $child = $expression->getExpressions()[0];

        $this->assertInstanceOf(EqExpression::class, $child);
        $this->assertEquals('key', $child->getKey());
        $this->assertEquals('ref', $child->getReference());
    }

    public function testMakeNinExpression()
    {
        $expression = $this->factory->make(Type::NIN, 'key', ['ref']);

        $this->assertInstanceOf(NotExpression::class, $expression);
        $this->assertCount(1, $expression->getExpressions());

        $child = $expression->getExpressions()[0];

        $this->assertInstanceOf(InExpression::class, $child);
        $this->assertEquals('key', $child->getKey());
        $this->assertEquals(['ref'], $child->getReference());
    }

    /** @expectedException \Exception */
    public function testThrowsExceptionIfUnknownOperator()
    {
        $this->factory->make('$unknown');
    }
}