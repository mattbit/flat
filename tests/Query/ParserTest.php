<?php

use Mockery as m;
use Mattbit\Flat\Query\Parser;
use Mattbit\Flat\Query\Expression\Type;
use Mattbit\Flat\Query\Expression\Leaf\InExpression;
use Mattbit\Flat\Query\Expression\Leaf\EqExpression;
use Mattbit\Flat\Query\Expression\Leaf\GtExpression;
use Mattbit\Flat\Query\Expression\Tree\OrExpression;
use Mattbit\Flat\Query\Expression\Tree\AndExpression;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * The Expression\Factory mock.
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = m::mock(\Mattbit\Flat\Query\Expression\Factory::class);
        $this->parser = new Parser($this->factory);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testParseEqualityExpression()
    {
        $expression = $this->parser->parse([
            'field' => 'value'
        ]);

        $this->assertInstanceOf(EqExpression::class, $expression);
        $this->assertEquals('field', $expression->getKey());
        $this->assertEquals('value', $expression->getReference());
    }

    public function testParseLeafExpression()
    {
        $this->factory->shouldReceive('make')
            ->with(Type::EQ, 'field', 2);

        $this->parser->parse([
            'field' => ['$eq' => 2]
        ]);
    }

    public function testWrapMultipleExpressionsWithAnd()
    {
        $this->factory->shouldReceive('make')
            ->with(Type::EQ, 'field_one', 1)
            ->andReturn(new StubExpression());

        $this->factory->shouldReceive('make')
            ->with(Type::GT, 'field_two', 2)
            ->andReturn(new StubExpression());

        $expression = $this->parser->parse([
            'field_one' => ['$eq' => 1],
            'field_two' => ['$gt' => 2],
        ]);

        $this->assertInstanceOf(AndExpression::class, $expression);

        $children = $expression->getExpressions();
        $this->assertCount(2, $children);
    }

    public function testParseNestedFields()
    {
        $this->factory->shouldReceive('make')
            ->with(Type::EQ, 'user.age', 18);

        $this->parser->parse([
            'user' => [
                'age' => ['$eq' => 18]
            ]
        ]);
    }

    public function testWrapNestedExpressionsWithAnd()
    {
        $expression = $this->parser->parse([
            'user' => [
                'name' => 'John',
                'age' => 18,
            ]
        ]);

        $this->assertInstanceOf(AndExpression::class, $expression);
    }

    public function testParseOrExpressions()
    {
        $this->factory->shouldReceive('make')
            ->with(Type::OR_MATCH, m::any(), m::any())
            ->andReturn(new OrExpression());

        $this->factory->shouldReceive('make')
            ->with(Type::EQ, 'name', 'John')
            ->andReturn(new StubExpression());

        $this->factory->shouldReceive('make')
            ->with(Type::EQ, 'name', 'Joe')
            ->andReturn(new StubExpression());

        $expression = $this->parser->parse([
            'name' => [
                '$or' => [
                    [ '$eq' => 'John' ],
                    [ '$eq' => 'Joe']
                ]
            ]
        ]);

        $this->assertInstanceOf(OrExpression::class, $expression);
        $this->assertCount(2, $expression->getExpressions());
    }

    public function testParseAndExpression()
    {
        $this->factory->shouldReceive('make')
            ->with(Type::AND_MATCH, m::any(), m::any())
            ->andReturn(new AndExpression());

        $this->factory->shouldReceive('make')
            ->with(Type::EQ, 'user.name', 'John')
            ->andReturn(new StubExpression());

        $this->factory->shouldReceive('make')
            ->with(Type::GT, 'user.age', 18)
            ->andReturn(new StubExpression());

        $expression = $this->parser->parse([
            'user' => [
                '$and' => [
                    ['name' => [ '$eq' => 'John' ]],
                    ['age' => [ '$gt' => 18]]
                ]
            ]
        ]);

        $this->assertInstanceOf(AndExpression::class, $expression);
        $this->assertCount(2, $expression->getExpressions());
    }

    /** @expectedException \Exception */
    public function testThrowsExceptionWithInvalidTreeExpressionElement()
    {
        $this->parser->parse([
            '$and' => 1
        ]);
    }
}

class StubExpression implements \Mattbit\Flat\Query\Expression\ExpressionInterface {
    public function match(\Mattbit\Flat\Document\Matchable $document) {
        return true;
    }
}
