<?php

use Mattbit\Flat\Query\Expression\Leaf\EqExpression;
use Mattbit\Flat\Query\Expression\Leaf\GtExpression;
use Mattbit\Flat\Query\Expression\Leaf\LtExpression;
use Mattbit\Flat\Query\Expression\Leaf\GteExpression;
use Mattbit\Flat\Query\Expression\Leaf\LteExpression;
use Mattbit\Flat\Query\Expression\Leaf\InExpression;
use Mattbit\Flat\Query\Expression\Leaf\RegexExpression;

class LeafExpressionTest extends PHPUnit_Framework_TestCase
{
    public function testEqExpression()
    {
        $expr = new EqExpression('value', 3);

        $this->assertFalse($expr->match($this->docWithValue(2)));
        $this->assertTrue($expr->match($this->docWithValue(3)));
        $this->assertFalse($expr->match($this->docWithValue('3')));
        $this->assertFalse($expr->match($this->docWithValue(4)));
    }

    public function testGtExpression()
    {
        $expr = new GtExpression('value', 3);

        $this->assertFalse($expr->match($this->docWithValue(2)));
        $this->assertFalse($expr->match($this->docWithValue(3)));
        $this->assertTrue($expr->match($this->docWithValue(4)));
    }

    public function testGteExpression()
    {
        $expr = new GteExpression('value', 3);

        $this->assertFalse($expr->match($this->docWithValue(2)));
        $this->assertTrue($expr->match($this->docWithValue(3)));
        $this->assertTrue($expr->match($this->docWithValue(4)));
    }

    public function testLtExpression()
    {
        $expr = new LtExpression('value', 3);

        $this->assertTrue($expr->match($this->docWithValue(2)));
        $this->assertFalse($expr->match($this->docWithValue(3)));
        $this->assertFalse($expr->match($this->docWithValue(4)));
    }

    public function testLteExpression()
    {
        $expr = new LteExpression('value', 3);

        $this->assertTrue($expr->match($this->docWithValue(2)));
        $this->assertTrue($expr->match($this->docWithValue(3)));
        $this->assertFalse($expr->match($this->docWithValue(4)));
    }

    public function testInExpression()
    {
        $expr = new InExpression('value', ['a', 'b']);

        $this->assertTrue($expr->match($this->docWithValue('a')));
        $this->assertTrue($expr->match($this->docWithValue('b')));
        $this->assertFalse($expr->match($this->docWithValue('c')));
    }

    public function testRegexExpression()
    {
        $expr = new RegexExpression('value', '~<p>.*</p>~');

        $this->assertTrue($expr->match($this->docWithValue('<p>paragraph</p>')));
        $this->assertFalse($expr->match($this->docWithValue('<p>false')));
        $this->assertFalse($expr->match($this->docWithValue('and false')));
    }

    protected function docWithValue($value)
    {
        return new Mattbit\Flat\Document\Document([
            'value' => $value
        ]);
    }
}