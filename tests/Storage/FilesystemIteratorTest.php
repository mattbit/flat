<?php

use Mockery as m;
use Mattbit\Flat\Model\Document;
use Mattbit\Flat\Storage\EncoderInterface;
use Mattbit\Flat\Storage\FilesystemIterator;

use org\bovigo\vfs\vfsStream as vfs;

class FilesystemIteratorTest extends PHPUnit_Framework_TestCase
{
    protected $encoder;

    protected $iterator;

    protected $glob;

    public function setup()
    {
        $this->encoder = m::mock(EncoderInterface::class);
        $this->iterator = new FilesystemIterator($this->encoder, 'test');
        $this->glob = m::mock(Iterator::class);
        $this->iterator->setIterator($this->glob);
    }

    public function testCurrentReturnsDocument()
    {
        vfs::setup('test', null, ['doc.data' => 'test_data']);

        $this->glob->shouldReceive('current')
            ->once()
            ->andReturn('vfs://test/doc.data');

        $this->encoder->shouldReceive('decode')
            ->once()
            ->with('test_data')
            ->andReturn('decoded');

        $this->assertEquals('decoded', $this->iterator->current());
    }

    public function testKey()
    {
        $this->glob->shouldReceive('key')->once();
        $this->iterator->key();
    }

    public function testValid()
    {
        $this->glob->shouldReceive('valid')->once();
        $this->iterator->valid();
    }

    public function testRewind()
    {
        $this->glob->shouldReceive('rewind')->once();
        $this->iterator->rewind();
    }

    public function testNext()
    {
        $this->glob->shouldReceive('next')->once();
        $this->iterator->next();
    }
}
