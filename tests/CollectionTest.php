<?php

use Mockery as m;
use Mattbit\Flat\Database;
use Mattbit\Flat\Collection;
use Mattbit\Flat\Query\Parser;
use Mattbit\Flat\Document\Document;
use Mattbit\Flat\Storage\DocumentStore;
use Mattbit\Flat\Query\Expression\ExpressionInterface;

class CollectionTest extends PHPUnit_Framework_TestCase
{
    protected $store;

    protected $parser;

    protected $database;

    protected $collection;

    public function setUp()
    {
        $this->parser = m::mock(Parser::class);
        $this->database = m::mock(Database::class);
        $this->database->shouldReceive('getParser')->andReturn($this->parser);
        $this->store = m::mock(DocumentStore::class);
        $this->collection = new Collection($this->database, $this->store, 'test');
    }

    public function tearDown()
    {
        m::close();
    }

    public function testDrop()
    {
        $this->database->shouldReceive('dropCollection')
            ->once()
            ->with('test');

        $this->collection->drop();
    }

    public function testTruncate()
    {
        $this->store->shouldReceive('truncate')->once();

        $this->collection->truncate();
    }

    public function testInsert()
    {
        $document = new Document();

        $this->store->shouldReceive('insertDocument')
            ->once()
            ->with($document);

        $this->collection->insert($document);
    }

    public function testRemove()
    {
        $this->parser->shouldReceive('parse')
            ->once()
            ->with(['field' => 'test'])
            ->andReturn(m::mock(ExpressionInterface::class));

        $doc1 = new Document(['_id' => 1]);
        $doc2 = new Document(['_id' => 2]);

        $this->store->shouldReceive('scanDocuments')
            ->andReturn([$doc1, $doc2]);

        $this->store->shouldReceive('removeDocument')
            ->once()
            ->with(1);
        $this->store->shouldReceive('removeDocument')
            ->once()
            ->with(2);

        $this->collection->remove(['field' => 'test']);
    }

    public function testFind()
    {
        $this->parser->shouldReceive('parse')
            ->once()
            ->with(['field' => 'test'])
            ->andReturn(m::mock(ExpressionInterface::class));

        $doc1 = new Document(['_id' => 1]);
        $doc2 = new Document(['_id' => 2]);

        $this->store->shouldReceive('scanDocuments')
            ->andReturn([$doc1, $doc2]);

        $this->assertEquals([$doc1, $doc2], $this->collection->find(['field' => 'test']));
    }
}
