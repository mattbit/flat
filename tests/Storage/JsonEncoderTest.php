<?php

use Mattbit\Flat\Document\Document;
use Mattbit\Flat\Storage\JsonEncoder;

class JsonEncoderTest extends PHPUnit_Framework_TestCase
{
    protected $encoder;

    public function setUp()
    {
        $this->encoder = new JsonEncoder();
    }

    public function testEncode()
    {
        $document = new Document(['test' => true]);

        $this->assertEquals('{"test":true}', $this->encoder->encode($document));
    }

    public function testDecode()
    {
        $document = new Document(['test' => true]);

        $this->assertEquals($document, $this->encoder->decode('{"test":true}'));

    }

    public function testExtension()
    {
        $this->assertEquals('json', $this->encoder->getExtension());
    }
}