<?php

use Mattbit\Flat\Model\Date;
use Mattbit\Flat\Model\Document;
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
        $document = new Document(['test' => true, 'nested' => ['attribute' => 4]]);
        $encoded = $this->encoder->encode($document);
        $data = json_decode($encoded, true);

        $this->assertArrayHasKey('_doc', $data);
        $this->assertEquals(['test' => true, 'nested' => ['attribute' => 4]], $data['_doc']);
    }

    public function testDecode()
    {
        $document = new Document(['test' => true]);

        $this->assertEquals($document, $this->encoder->decode('{"_doc":{"test":true}}'));
    }

    public function testDatePreservation()
    {
        $original = new Document(['test' => true, 'my_date' => new Date()]);

        $data = $this->encoder->encode($original);
        $document = $this->encoder->decode($data);

        $this->assertEquals($original->get('my_date'), $document->get('my_date'));
    }

    /** @expectedException \Mattbit\Flat\Exception\DecodeException */
    public function testDecodeFailure()
    {
        $this->encoder->decode('{"test":false}');
    }
}
