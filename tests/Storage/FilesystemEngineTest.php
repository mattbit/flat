<?php

use Mockery as m;
use Mattbit\Flat\Model\Document;
use Mattbit\Flat\Storage\EncoderInterface;
use Mattbit\Flat\Storage\FilesystemEngine;

use org\bovigo\vfs\vfsStream as vfs;

class FilesystemEngineTest extends PHPUnit_Framework_TestCase
{
    protected $encoder;

    protected $engine;

    protected $root;

    public function setup()
    {
        $this->root = vfs::setup('test', null, [
            'collection' => [
                'testdoc.data' => 'test doc content',
            ],
        ]);
        $this->encoder = m::mock(EncoderInterface::class);
        $this->engine = new FilesystemEngine('vfs://test', $this->encoder);
    }

    public function testEncoder()
    {
        $this->assertSame($this->encoder, $this->engine->getEncoder());
    }

    public function testInit()
    {
        $this->engine->init('test_collection');

        $this->assertTrue($this->root->hasChild('test_collection'));

        $this->assertTrue($this->engine->init('collection'));
    }

    public function testPut()
    {
        $document = new Document();

        $this->encoder->shouldReceive('encode')
            ->once()
            ->with($document)
            ->andReturn('test_data');

        vfs::newDirectory('test/collection');

        $this->engine->put($document, 'doc_id', 'collection');

        $content = $this->root->getChild('collection/doc_id.data')->getContent();

        $this->assertEquals('test_data', $content);
    }

    public function testRetrieval()
    {
        $this->assertFalse($this->engine->has('nodoc', 'collection'));
        $this->assertTrue($this->engine->has('testdoc', 'collection'));

        $this->encoder->shouldReceive('decode')
            ->once()
            ->with('test doc content')
            ->andReturn('decoded content');

        $this->assertEquals('decoded content', $this->engine->get('testdoc', 'collection'));
    }

    public function testDelete()
    {
        $this->engine->delete('testdoc', 'collection');

        $this->assertEmpty($this->root->getChild('collection')->getChildren());
    }
}
