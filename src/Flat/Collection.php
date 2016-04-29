<?php

namespace Mattbit\Flat;

use Mattbit\Flat\Query\Matcher;
use Mattbit\Flat\Document\Identifiable;
use Mattbit\Flat\Query\Parser;
use Mattbit\Flat\Storage\DocumentStore;
use Mattbit\Flat\Query\Expression\ExpressionInterface;

class Collection
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
        return $this->database->dropCollection($this);
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
     * @param Identifiable $document
     *
     * @return bool
     */
    public function insert(Identifiable $document)
    {
        return $this->store->insertDocument($document);
    }

    /**
     * Update existing documents.
     *
     * @param array        $criteria
     * @param Identifiable $updated
     * @param bool         $multiple
     *
     * @return int The count of the documents updated.
     */
    public function update($criteria, Identifiable $updated, $multiple = false)
    {
        $limit = $multiple ? 1 : null;
        $documents = $this->onMatch($criteria, $limit);

        foreach ($documents as $document) {
            $this->store->updateDocument($updated);
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
            $this->store->removeDocument($document->getId());
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

        $documents = $this->store->scanDocuments([$matcher, 'match'], $limit);

        return $documents;
    }

    protected function newMatcher(ExpressionInterface $expression)
    {
        return new Matcher($expression);
    }
}
