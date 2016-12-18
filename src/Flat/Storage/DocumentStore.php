<?php

namespace Mattbit\Flat\Storage;

use Mattbit\Flat\Model\DocumentInterface;
use Mattbit\Flat\Storage\Filesystem\FilesystemCursor;

use Mattbit\Flat\Storage\EncoderInterface;

use Mattbit\Flat\Exception\DuplicateKeyException;

class DocumentStore implements \IteratorAggregate
{
    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * Create a DocumentStore instance.
     *
     * @param EngineInterface $engine
     * @param string $namespace
     */
    public function __construct(EngineInterface $engine, $namespace)
    {
        $this->engine = $engine;
        $this->namespace = $namespace;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getEngine()
    {
        return $this->engine;
    }

    public function truncate()
    {
        return $this->engine->clear($this->namespace);
    }

    public function insert(DocumentInterface $document)
    {
        if (!$id = $document->getId()) {
            $id = $this->generateId();
            $document->setId($id);
        }

        if ($this->engine->has($id, $this->namespace)) {
            throw new DuplicateKeyException("Cannot insert document with duplicate key: {$id}");
        }

        $this->engine->put($document, $id, $this->namespace);

        return $id;
    }

    public function update(DocumentInterface $document)
    {
        if (!$id = $document->getId()) {
            throw new \Exception("Cannot update a document without _id!");
        }

        return $this->engine->put($document, $id, $this->namespace);
    }

    public function remove($id)
    {
        return $this->engine->delete($id, $this->namespace);
    }

    public function find($id)
    {
        return $this->engine->get($id, $this->namespace);
    }

    public function scan(callable $filter = null, $limit = null)
    {
        $documents = [];

        $index = 0;
        foreach ($this->engine->all($this->namespace) as $document) {
            if ($limit && $index >= $limit) {
                break;
            }
            $index += 1;

            if (!$filter || call_user_func($filter, $document)) {
                $documents[] = $document;
            }
        }

        return $documents;
    }

    public function insertDocument($namespace) {}
    public function removeDocument($id) {}
    public function createCollection($collection) {}
    public function dropCollection($collection) {}

    protected function generateId()
    {
        // A simple uniqid should have enough entropy.
        // @todo: evaluate the usage of ramsey/uuid
        return uniqid();
    }

    public function getIterator()
    {
        return $this->engine->createCursor($this);
    }
}
