<?php

use Mattbit\Flat\Model\Document;

class DocumentTest extends PHPUnit_Framework_TestCase
{
    public function testInitialization()
    {
        $document = new Document(['test' => 'ok']);

        $this->assertEquals('ok', $document->get('test'));
    }

    public function testAttributes()
    {
        $document = new Document(['test' => 'ok']);

        $this->assertEquals('ok', $document->get('test'));
        $this->assertNull($document->get('unknown'));

        $this->assertTrue($document->has('test'));
        $this->assertFalse($document->has('unknown'));
    }

    public function testNesting()
    {
        $document = new Document([
            'user' => [
                'name' => 'John',
                'email' => 'john@example.com'
            ]
        ]);

        $this->assertEquals('John', $document->get('user.name'));
        $this->assertEquals('john@example.com', $document->get('user.email'));

        $this->assertTrue($document->has('user.name'));
        $this->assertFalse($document->has('user.nothing'));

        $document->set('new.nested.field', 'hello');
        $document->set('user.likes.beer', true);

        $this->assertEquals('hello', $document->get('new.nested.field'));
        $this->assertTrue($document->get('user.likes.beer'));
        $this->assertEquals('John', $document->get('user.name'));
    }
}
