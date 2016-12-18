<?php

namespace Mattbit\Flat;

use Mattbit\Flat\Query\Parser;
use Mattbit\Flat\Query\Matcher;
use Mattbit\Flat\Storage\DocumentStore;
use Mattbit\Flat\Model\DocumentInterface;
use Mattbit\Flat\Query\Expression\ExpressionInterface;
use Traversable;

class Collection implements \IteratorAggregate
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var DocumentStore
     */
    protected $store;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * Construct a new collection.
     *
     * @param Database      $database
     * @param DocumentStore $store
     * @param string        $name
     */
    public function __construct(Database $database, DocumentStore $store, $name)
    {
        $this->name = $name;
        $this->store = $store;
        $this->database = $database;
        $this->parser = $database->getParser();
    }

    /**
     * Drop the collection.
     *
     * @return bool
     */
    public function drop()
    {
        return $this->database->dropCollection($this->name);
    }

    /**
     * Remove all the documents from the collection.
     *
     * @return bool
     */
    public function truncate()
    {
        $this->store->truncate();
    }

    /**
     * Insert a new document.
     *
     * @param DocumentInterface $document
     *
     * @return bool
     */
    public function insert(DocumentInterface $document)
    {
        return $this->store->insert($document);
    }

    /**
     * Update existing documents.
     *
     * @param array        $criteria
     * @param DocumentInterface $updated
     * @param bool         $multiple
     *
     * @return int The count of the documents updated.
     */
    public function update($criteria, DocumentInterface $updated, $multiple = false)
    {
        $limit = $multiple ? 1 : null;
        $documents = $this->onMatch($criteria, $limit);

        foreach ($documents as $document) {
            $this->store->update($updated);
        }
    }

    /**
     * Remove documents from the collection.
     *
     * @param mixed $criteria
     * @param bool  $multiple
     *
     * @return int The count of the document deleted.
     */
    public function remove($criteria, $multiple = false)
    {
        $limit = $multiple ? 1 : null;

        $documents = $this->onMatch($criteria, $limit);

        foreach ($documents as $document) {
            $this->store->remove($document->get('_id'));
        }

        return true;
    }

    /**
     * Find documents in the collection.
     *
     * @param array $criteria
     *
     * @return array The array of results.
     */
    public function find($criteria)
    {
        return $this->onMatch($criteria);
    }

    protected function onMatch($criteria, $limit = null)
    {
        $expression = $this->parser->parse($criteria);
        $matcher = $this->newMatcher($expression);

        $documents = $this->store->scan([$matcher, 'match'], $limit);

        return $documents;
    }

    protected function newMatcher(ExpressionInterface $expression)
    {
        return new Matcher($expression);
    }

    public function getIterator()
    {
        return $this->store->getIterator();
    }
}
