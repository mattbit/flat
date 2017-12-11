<?php

use Mockery as m;
use Mattbit\Flat\Model\Document;
use Mattbit\Flat\Storage\Engine;
use Mattbit\Flat\Storage\DocumentStore;
use Mattbit\Flat\Model\DocumentInterface;
use Mattbit\Flat\Storage\EngineInterface;
use Mattbit\Flat\Storage\EncoderInterface;
use Mattbit\Flat\Exception\DuplicateKeyException;

class DocumentStoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var DocumentStore
     */
    protected $store;

    public function setUp()
    {
        $this->engine = m::mock(EngineInterface::class);
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
        $this->engine->shouldReceive('clear')
            ->once()
            ->andReturn(true);

        $this->assertTrue($this->store->truncate());
    }

    public function testInsert()
    {
        $document = new Document(['_id' => 'doc_id']);

        $this->engine->shouldReceive('has')
            ->once()
            ->with('doc_id', 'test')
            ->andReturn(false);

        $this->engine->shouldReceive('put')
            ->once()
            ->with($document, 'doc_id', 'test');

        $this->assertEquals('doc_id', $this->store->insert($document));
    }

    public function testInsertWithoutId()
    {
        $document = new Document();

        $this->engine->shouldReceive('has')
            ->once()
            ->andReturn(false);

        $this->engine->shouldReceive('put')
            ->once()
            ->with($document, m::type('string'), 'test');

        $this->assertNotNull($this->store->insert($document));
    }

    /** @expectedException Mattbit\Flat\Exception\DuplicateKeyException */
    public function testInsertDuplicate()
    {
        $document = new Document(['_id' => '1']);

        $this->engine->shouldReceive('has')
            ->once()
            ->with('1', 'test')
            ->andReturn(true);

        $this->store->insert($document);
    }

    public function testUpdate()
    {
        $document = new Document(['_id' => '1']);


        $this->engine->shouldReceive('put')
            ->once()
            ->with($document, '1', 'test');

        $this->store->update($document);
    }

    /** @expectedException \Exception */
    public function testUpdateWithoutId()
    {
        $document = new Document();

        $this->store->update($document);
    }

    public function testRemove()
    {
        $this->engine->shouldReceive('delete')
            ->once()
            ->with('doc_id', 'test');

        $this->store->remove('doc_id');
    }

    public function testFind()
    {
        $this->engine->shouldReceive('get')
            ->once()
            ->with('doc_id', 'test')
            ->andReturn('document');

        $this->assertEquals('document', $this->store->find('doc_id'));
    }

    public function testScan()
    {
        $this->engine->shouldReceive('all')
            ->once()
            ->andReturn(new ArrayIterator(['doc_1', 'doc_2', 'doc_3']));

        $this->assertEquals(['doc_1', 'doc_2', 'doc_3'], $this->store->scan());
    }

    public function testScanLimit()
    {
        $this->engine->shouldReceive('all')
            ->once()
            ->andReturn(['doc_1', 'doc_2', 'doc_3']);

        $documents = $this->store->scan(null, 2);

        $this->assertEquals(['doc_1', 'doc_2'], $documents);
    }

    public function testScanFilter()
    {
        $this->engine->shouldReceive('all')
            ->once()
            ->andReturn(new ArrayIterator(['doc_1', 'doc_2', 'doc_3']));

        $documents = $this->store->scan(function($doc) {
            return $doc !== 'doc_2';
        });

        $this->assertEquals(['doc_1', 'doc_3'], $documents);
    }

    public function testCount()
    {
        $this->engine->shouldReceive('all')
            ->once()
            ->andReturn(new ArrayIterator(['one', 'two', 'three']));

        $this->assertEquals(3, $this->store->count());

        // Empty list
        $this->engine->shouldReceive('all')
            ->once()
            ->andReturn(new ArrayIterator());

        $this->assertEquals(0, $this->store->count());
    }
}
