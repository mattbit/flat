<?php

namespace Mattbit\Flat;

use Mattbit\Flat\Document\Document;

class Collection
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Construct a new collection.
     *
     * @param string           $name
     * @param StorageInterface $storage
     */
    public function __construct($name, StorageInterface $storage)
    {
        $this->name = $name;
        $this->storage = $storage;
    }

    /**
     * Drop the collection.
     *
     * @return bool
     */
    public function drop()
    {
    }

    /**
     * Remove all the documents from the collection.
     *
     * @return bool
     */
    public function truncate()
    {
    }

    /**
     * Insert a new document.
     *
     * @param array|Document $document
     *
     * @return bool Successfull insertion.
     */
    public function insert($document)
    {
        if ($document instanceof Document) {
            $document = $document->toArray();
        }

        return $this->storage->insert($document);
    }

    /**
     * Update existing documents.
     *
     * @param mixed $criteria
     * @param mixed $updates
     * @param bool  $multiple
     *
     * @return int The count of the documents updated.
     */
    public function update($criteria, $updates, $multiple = false)
    {
        $this->storage->update($criteria, $updates, $multiple);
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
        return $this->storage->remove($criteria, $multiple);
    }

    /**
     * Find documents in the collection.
     *
     * @param mixed $criteria
     *
     * @return array The array of results.
     */
    public function find($criteria)
    {
        $results = [];

        foreach ($this->storage->find($criteria) as $data) {
            $results[] = new Document($data);
        }

        return $results;
    }
}
