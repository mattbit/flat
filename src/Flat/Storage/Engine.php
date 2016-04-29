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
     * @var EncoderInterface
     */
    protected $encoder;

    public function __construct(FilesystemInterface $filesystem, EncoderInterface $encoder)
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

        return $this->createDocumentStore($collection);
    }
}
