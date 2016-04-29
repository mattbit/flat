<?php

namespace Mattbit\Flat\Storage;

use Mattbit\Flat\Query\Parser;
use Mattbit\Flat\Query\Matcher;
use Mattbit\Flat\Document\Document;
use League\Flysystem\FilesystemInterface;

class Engine
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var Encoder
     */
    protected $encoder;
    
    public function __construct(FilesystemInterface $filesystem, JsonEncoder $encoder)
    {
        $this->filesystem = $filesystem;
        $this->encoder = $encoder;
    }

    /**
     * Get the Filesystem instance.
     *
     * @return FilesystemInterface
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }


    /**
     * Get the EncoderInterface instance.
     *
     * @return EncoderInterface
     */
    public function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * Create a new DocumentStore.
     *
     * @param string $namespace
     * @return DocumentStore
     */
    public function createDocumentStore($namespace)
    {
        return new DocumentStore($this, $namespace);
    }

    public function dropCollection($collection)
    {
        $this->filesystem->deleteDir($collection);
    }

    public function createCollection($collection)
    {
        $this->filesystem->createDir($collection);
    }

    public function truncateCollection($collection)
    {
        $this->dropCollection($collection);
        $this->createCollection($collection);
    }

    public function update($collection, array $criteria, array $updates, $multiple = false)
    {
        return $this->onMatch($collection, $criteria, function ($document) use ($updates) {
            $document = array_merge($document, $updates);

            return $this->insert($document);
        });
    }

    public function remove($collection, array $criteria, $multiple = false)
    {
        return $this->onMatch($collection, $criteria, function ($document, $path) {
            return $this->filesystem->delete($path);
        });
    }

    public function find($collection, array $criteria)
    {
        $results = [];

        $this->onMatch($collection, $criteria, function ($document) use (&$results) {
            $results[] = $document;
        }, true);

        return $results;
    }

    public function all($collection)
    {
        $results = [];

        foreach ($this->filesystem->listContents($collection) as $meta) {
            $data = $this->filesystem->read($meta['path']);
            $results[] = new Document($this->encoder->decode($data));
        }

        return $results;
    }

    protected function path($collection, $id)
    {
        return sprintf('%s/%s.%s', $collection, $id, Encoder::EXTENSION);
    }

    protected function onMatch($collection, $criteria, \Closure $closure, $multiple = false)
    {
        $count = 0;

        $expression = $this->parser->parse($criteria);
        $matcher = new Matcher($expression);

        foreach ($this->filesystem->listContents($collection) as $meta) {
            $data = $this->filesystem->read($meta['path']);
            $document = new Document($this->encoder->decode($data));

            if ($matcher->match($document)) {
                $count += (int) call_user_func_array($closure, [$document, $meta]);

                if (!$multiple) {
                    break;
                }
            }
        };

        return $count;
    }
}
