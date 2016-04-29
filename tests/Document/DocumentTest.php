<?php

use Mattbit\Flat\Document\Document;

class DocumentTest extends PHPUnit_Framework_TestCase
{
    public function testInitialization()
    {
        $document = new Document(['test' => 'ok']);

        $this->assertEquals('ok', $document->get('test'));
    }

    public function testAttributes()
    {
        $document = new Document();
        $document->set('test', 'ok');

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
    }
}
