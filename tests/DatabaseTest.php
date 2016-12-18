<?php

use Mockery as m;
use Mattbit\Flat\Database;
use Mattbit\Flat\Storage\DocumentStore;
use Mattbit\Flat\Storage\EngineInterface;

class DatabaseTest extends PHPUnit_Framework_TestCase
{
    protected $engine;

    protected $database;

    public function setUp()
    {
        $this->engine = m::mock(EngineInterface::class);
        $this->database = new Database($this->engine);
    }

    public function testCreateCollection()
    {
        $this->engine->shouldReceive('init')
            ->once()
            ->with('test')
            ->andReturn(m::mock(DocumentStore::class));

        $collection = $this->database->createCollection('test');

        $this->assertSame($collection, $this->database->collection('test'));
    }
}
