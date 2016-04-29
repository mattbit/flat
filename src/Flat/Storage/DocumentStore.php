<?php

namespace Mattbit\Flat\Storage;

use Mattbit\Flat\Document\Document;
use Mattbit\Flat\Document\Identifiable;

class DocumentStore
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var EncoderInterface
     */
    protected $encoder;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $filesystem;

    /**
     * Create a RecordStore instance.
     *
     * @param Engine $engine
     * @param string $namespace
     */
    public function __construct(Engine $engine, $namespace)
    {
        $this->engine = $engine;
        $this->namespace = $namespace;
        $this->filesystem = $this->engine->getFilesystem();
        $this->encoder = $this->engine->getEncoder();
    }

    public function truncate()
    {
        $files = $this->filesystem->listContents($this->namespace);

        foreach ($files as $file) {
            $this->filesystem->delete($file['path']);
        }

        return true;
    }

    public function insertDocument(Identifiable $document)
    {
        $id = $document->getId() ?: $this->generateId();
        $path = $this->path($id);
        $data = $this->encoder->encode($document);

        if ($this->filesystem->has($this->path($id))) {
            throw new \Exception("Duplicate _id: {$id}");
        }


        $this->filesystem->put($path, $data);

        return $id;
    }

    public function insertDocuments(array $documents)
    {
        $ids = [];

        foreach ($documents as $document) {
            $ids[] = $this->insertDocument($document);
        }

        return $ids;
    }

    public function updateDocument(Identifiable $document)
    {
        if (!$id = $document->getId()) {
            throw new \Exception("Cannot update a document without _id!");
        }

        $data = $this->encoder->encode($document);
        $path = $this->path($id);

        return $this->filesystem->put($path, $data);
    }

    public function removeDocument($documentId)
    {
        $path = $this->path($documentId);

        return $this->filesystem->delete($path);
    }

    public function removeDocuments(array $documentIds)
    {
        foreach ($documentIds as $documentId) {
            $this->removeDocument($documentId);
        }

        return true;
    }

    public function findDocument($documentId)
    {
        $path = $this->path($documentId);
        $data = $this->filesystem->read($path);

        return $this->encoder->decode($data);
    }

    public function scanDocuments(callable $filter = null, $limit = null)
    {
        $files = $this->filesystem->listContents($this->namespace);
        $documents = [];

        foreach ($files as $index => $file) {
            if ($limit && $index > $limit) {
                break;
            }

            $data = $this->filesystem->read($file['path']);
            $document = $this->encoder->decode($data);

            if (!$filter || call_user_func($filter, $document)) {
                $documents[] = $document;
            }
        }

        return $documents;
    }

    protected function path($id)
    {
        return sprintf('%s/%s.%s', $this->namespace, $id, $this->encoder->getExtension());
    }

    protected function extractId($record)
    {
        if (isset($record['_id'])) {
            return $record['_id'];
        }
    }

    protected function generateId()
    {
        // A simple uniqid should have enough entropy.
        return uniqid();
    }

    protected function newDocument($attributes)
    {
        return new Document($attributes);
    }
}
