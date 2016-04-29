<?php

use League\Flysystem\FilesystemInterface;
use Mattbit\Flat\Storage\DocumentStore;
use Mattbit\Flat\Storage\EncoderInterface;
use Mattbit\Flat\Storage\Engine;
use Mockery as m;

class EngineTest extends PHPUnit_Framework_TestCase
{
    /**
     * Filesystem mock.
     */
    protected $filesystem;

    /**
     * Encoder mock.
     */
    protected $encoder;

    /**
     * The instance under test.
     *
     * @var Engine
     */
    protected $engine;

    public function setUp()
    {
        $this->filesystem = m::mock(FilesystemInterface::class);
        $this->encoder = m::mock(EncoderInterface::class);

        $this->engine = new Engine($this->filesystem, $this->encoder);
    }

    public function tearDown()
    {
        m::close();
    }

    public function testCreateDocumentStore()
    {
        $store = $this->engine->createDocumentStore('test');

        $this->assertInstanceOf(DocumentStore::class, $store);
        $this->assertEquals('test', $store->getNamespace());
    }

    public function testDropCollection()
    {
        $this->filesystem->shouldReceive('deleteDir')
                ->once()
                ->with('test');

        $this->engine->dropCollection('test');
    }

    public function testCreateCollection()
    {
        $this->filesystem->shouldReceive('createDir')
            ->once()
            ->with('test');

        $store = $this->engine->createCollection('test');

        $this->assertInstanceOf(DocumentStore::class, $store);
    }
}
