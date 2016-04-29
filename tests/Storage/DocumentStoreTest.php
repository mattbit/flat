<?php

use League\Flysystem\FilesystemInterface;
use Mattbit\Flat\Document\Document;
use Mattbit\Flat\Document\Identifiable;
use Mattbit\Flat\Storage\EncoderInterface;
use Mattbit\Flat\Storage\Engine;
use Mockery as m;
use Mattbit\Flat\Storage\DocumentStore;

class DocumentStoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * Filesystem mock
     */
    protected $filesystem;

    /**
     * Encoder mock
     */
    protected $encoder;

    /**
     * @var DocumentStore
     */
    protected $store;

    public function setUp()
    {
        $this->filesystem = m::mock(FilesystemInterface::class);
        $this->encoder = m::mock(EncoderInterface::class);

        $this->engine = new Engine($this->filesystem, $this->encoder);
        $this->store = new DocumentStore($this->engine, 'test');
    }

    public function tearDown()
    {
        m::close();
    }

    public function testCorrectNamespace()
    {
        $this->assertEquals('test', $this->store->getNamespace());
    }

    public function testTruncate()
    {
        $this->filesystem->shouldReceive('listContents')
            ->once()
            ->andReturn([
                ['path' => 'path_1'],
                ['path' => 'path_2']
            ]);

        $this->filesystem->shouldReceive('delete')
            ->once()
            ->with('path_1');

        $this->filesystem->shouldReceive('delete')
            ->once()
            ->with('path_2');

        $this->store->truncate();
    }

    public function testInsert()
    {
        $document = new Document(['_id' => 'doc_id']);

        $this->encoder->shouldReceive('getExtension')->andReturn('json');
        $this->encoder->shouldReceive('encode')
            ->with($document)
            ->andReturn('data');

        $this->filesystem->shouldReceive('has')->andReturn(false);
        $this->filesystem->shouldReceive('put')
            ->once()
            ->with('test/doc_id.json', 'data');

        $this->assertEquals('doc_id', $this->store->insertDocument($document));
    }

    public function testInsertWithoutId()
    {
        $document = new Document();

        $this->encoder->shouldReceive('getExtension')->andReturn('json');
        $this->encoder->shouldReceive('encode')
            ->with($document)
            ->andReturn('data');

        $this->filesystem->shouldReceive('has')->andReturn(false);
        $this->filesystem->shouldReceive('put')
            ->once()
            ->with(m::any(), 'data');

        $this->store->insertDocument($document);
    }

    /** @expectedException \Exception */
    public function testInsertDuplicate()
    {
        $document = new Document(['_id' => '1']);

        $this->encoder->shouldReceive('getExtension')->andReturn('json');

        $this->filesystem->shouldReceive('has')
            ->with('test/1.json')
            ->andReturn(true);

        $this->store->insertDocument($document);
    }

    public function testUpdate()
    {
        $document = new Document(['_id' => '1']);

        $this->encoder->shouldReceive('getExtension')->andReturn('json');
        $this->encoder->shouldReceive('encode')
            ->with($document)
            ->andReturn('updated_data');

        $this->filesystem->shouldReceive('put')
            ->once()
            ->with('test/1.json', 'updated_data');

        $this->store->updateDocument($document);
    }

    /** @expectedException \Exception */
    public function testUpdateWithoutId()
    {
        $document = new Document();

        $this->store->updateDocument($document);
    }

    public function testRemove()
    {
        $this->encoder->shouldReceive('getExtension')->andReturn('json');

        $this->filesystem->shouldReceive('delete')
            ->once()
            ->with('test/doc_id.json');

        $this->store->removeDocument('doc_id');
    }

    public function testFind()
    {
        $this->encoder->shouldReceive('getExtension')->andReturn('json');
        $this->encoder->shouldReceive('decode')
            ->with('encoded_data')
            ->once();

        $this->filesystem->shouldReceive('read')
            ->once()
            ->with('test/doc_id.json')
            ->andReturn('encoded_data');

        $this->store->findDocument('doc_id');
    }

    public function testScan()
    {
        $this->filesystem->shouldReceive('listContents')
            ->andReturn([
                ['path' => 'path_1'],
                ['path' => 'path_2'],
                ['path' => 'path_3'],
            ]);

        $this->filesystem->shouldReceive('read')
            ->with(m::anyOf('path_1', 'path_2', 'path_3'));

        $this->encoder->shouldReceive('getExtension')->andReturn('json');
        $this->encoder->shouldReceive('decode')
            ->andReturn('doc_1', 'doc_2', 'doc_3');

        $this->assertEquals(['doc_1', 'doc_2', 'doc_3'], $this->store->scanDocuments());
    }

    public function testScanLimit()
    {
        $this->filesystem->shouldReceive('listContents')
            ->andReturn([
                ['path' => 'path_1'],
                ['path' => 'path_2'],
                ['path' => 'path_3'],
            ]);

        $this->filesystem->shouldReceive('read')
            ->with(m::anyOf('path_1', 'path_2'));

        $this->encoder->shouldReceive('getExtension')->andReturn('json');
        $this->encoder->shouldReceive('decode')
            ->andReturn('doc_1', 'doc_2', 'doc_3');

        $documents = $this->store->scanDocuments(null, 2);

        $this->assertEquals(['doc_1', 'doc_2'], $documents);
    }

    public function testScanFilter()
    {
        $this->filesystem->shouldReceive('listContents')
            ->andReturn([
                ['path' => 'path_1'],
                ['path' => 'path_2'],
                ['path' => 'path_3'],
            ]);

        $this->filesystem->shouldReceive('read')
            ->with(m::anyOf('path_1', 'path_2', 'path_3'));

        $this->encoder->shouldReceive('getExtension')->andReturn('json');
        $this->encoder->shouldReceive('decode')
            ->andReturn('doc_1', 'doc_2', 'doc_3');

        $documents = $this->store->scanDocuments(function($doc) {
            return $doc !== 'doc_2';
        });

        $this->assertEquals(['doc_1', 'doc_3'], $documents);
    }
}
