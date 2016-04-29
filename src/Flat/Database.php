<?php

namespace Mattbit\Flat;

use Mattbit\Flat\Query\Expression\Factory;
use Mattbit\Flat\Query\Parser;
use Mattbit\Flat\Storage\Engine;

class Database
{
    /**
     * The storage engine.
     *
     * @var Engine
     */
    protected $engine;

    protected $parser;

    /**
     * An array of the registered collections.
     *
     * @var array
     */
    protected $collections;

    public function __construct(Engine $engine)
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
        $store = $this->engine->createDocumentStore($name);

        return $this->collections[$name] = new Collection($this, $store, $name);
    }

    public function dropCollection($name)
    {
        $this->engine->removeCollection($name);
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
}
