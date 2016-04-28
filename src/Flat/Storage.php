<?php

namespace Mattbit\Flat;

use Mattbit\Flat\Query\Matcher;
use Mattbit\Flat\Document\Document;
use League\Flysystem\FilesystemInterface;

class Storage
{
    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var Encoder
     */
    protected $encoder;

    protected $queryParser;

    public function __construct(FilesystemInterface $filesystem, Encoder $encoder)
    {
        $this->filesystem = $filesystem;
        $this->encoder = $encoder;
    }

    public function removeCollection($collection)
    {
        $this->filesystem->deleteDir($collection);
    }

    public function createCollection($collection, $meta = [])
    {
        $this->filesystem->createDir($collection);
    }

    public function truncateCollection($collection)
    {
        $this->filesystem->deleteDir($collection);
        $this->createCollection($collection);
    }

    public function insert($collection, array $document)
    {
        if (!isset($document['_id'])) {
            $document['_id'] = sha1(uniqid('', true));
        }

        $data = $this->encoder->encode($document);
        $destination = $this->path($collection, $document['_id']);

        return $this->filesystem->write($destination, $data);
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

        $matcher = new Matcher($criteria);

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
