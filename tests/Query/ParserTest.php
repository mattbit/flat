<?php

use Mattbit\Flat\Query\Expression\Leaf\InExpression;
use Mattbit\Flat\Query\Parser;
use Mattbit\Flat\Query\Expression\Type;
use Mattbit\Flat\Query\Expression\Leaf\EqExpression;
use Mattbit\Flat\Query\Expression\Leaf\GtExpression;
use Mattbit\Flat\Query\Expression\Tree\AndExpression;
use Mattbit\Flat\Query\Expression\Tree\OrExpression;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    public function setUp()
    {
        // @todo: mock the factory
        $this->parser = new Parser(new \Mattbit\Flat\Query\Expression\Factory());
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
        $expression = $this->parser->parse([
            'field' => ['$eq' => 2]
        ]);

        $this->assertInstanceOf(EqExpression::class, $expression);
        $this->assertEquals('field', $expression->getKey());
        $this->assertEquals(2, $expression->getReference());
    }

    public function testWrapMultipleExpressionsWithAnd()
    {
        $expression = $this->parser->parse([
            'field_one' => ['$eq' => 1],
            'field_two' => ['$gt' => 2],
        ]);

        $this->assertInstanceOf(AndExpression::class, $expression);

        $children = $expression->getExpressions();
        $this->assertCount(2, $children);
        $this->assertInstanceOf(EqExpression::class, $children[0]);
        $this->assertEquals('field_one', $children[0]->getKey());
        $this->assertEquals(1, $children[0]->getReference());
    }

    public function testParseNestedFields()
    {
        $expression = $this->parser->parse([
            'user' => [
                'age' => ['$eq' => 18]
            ]
        ]);

        $this->assertInstanceOf(EqExpression::class, $expression);
        $this->assertEquals('user.age', $expression->getKey());
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

    public function testParseTreeExpressions()
    {
        $expression = $this->parser->parse([
            'name' => [
                '$or' => [
                    [ '$eq' => 'John' ],
                    [ '$eq' => 'Joe']
                ]
            ]
        ]);

        $this->assertInstanceOf(OrExpression::class, $expression);

        $children = $expression->getExpressions();

        $this->assertCount(2, $children);
        $this->assertInstanceOf(EqExpression::class, $children[0]);

        $expression = $this->parser->parse([
            'user' => [
                '$and' => [
                    'name' => [ '$eq' => 'John' ],
                    'age' => [ '$gt' => 18]
                ]
            ]
        ]);

        $this->assertInstanceOf(AndExpression::class, $expression);

        $children = $expression->getExpressions();

        $this->assertCount(2, $children);
        $this->assertInstanceOf(EqExpression::class, $children[0]);
    }

    public function testParseArrayExpression()
    {
        $expression = $this->parser->parse([
            'name' => [
                '$in' => ['John', 'Joe']
            ]
        ]);

        $this->assertInstanceOf(InExpression::class, $expression);
        $this->assertEquals(['John', 'Joe'], $expression->getReference());
        $this->assertEquals('name', $expression->getKey());
    }

    /** @expectedException \Exception */
    public function testThrowsExceptionWithInvalidTreeExpressionElement()
    {
        $this->parser->parse([
            '$and' => 1
        ]);
    }

}
