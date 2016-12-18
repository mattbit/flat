<?php

namespace Mattbit\Flat;

use Mattbit\Flat\Query\Parser;
use Mattbit\Flat\Storage\DocumentStore;
use Mattbit\Flat\Storage\EngineInterface;
use Mattbit\Flat\Query\Expression\Factory;

class Database
{
    /**
     * The storage engine.
     *
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * An array of the registered collections.
     *
     * @var array
     */
    protected $collections;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
        $this->initializeParser();
    }

    /**
     * Select an existing collection or create a new one.
     *
     * @param string $name
     *
     * @return Collection
     */
    public function collection($name)
    {
        return $this->getOrCreateCollection($name);
    }

    public function getOrCreateCollection($name)
    {
        if (isset($this->collections[$name])) {
            return $this->collections[$name];
        }

        return $this->createCollection($name);
    }

    public function createCollection($name)
    {
        $this->engine->init($name);
        $store = new DocumentStore($this->engine, $name);

        return $this->collections[$name] = $this->newCollection($store, $name);
    }

    public function dropCollection($name)
    {
        $this->engine->destroy($name);
        unset($this->collections[$name]);

        return true;
    }

    public function getParser()
    {
        return $this->parser;
    }

    protected function initializeParser()
    {
        $factory = new Factory();
        $this->parser = new Parser($factory);
    }

    protected function newCollection(DocumentStore $store, $name)
    {
        return new Collection($this, $store, $name);
    }
}
