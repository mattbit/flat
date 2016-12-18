<?php

namespace Mattbit\Flat\Storage;

use Mattbit\Flat\Model\Document;
use Mattbit\Flat\Storage\EncoderInterface;

class FilesystemIterator implements \Iterator {

    /**
     * @var EncoderInterface
     */
    protected $encoder;

    public function __construct(EncoderInterface $encoder, $path)
    {
        $this->encoder = $encoder;
        $this->iterator = new \GlobIterator($path, \FilesystemIterator::CURRENT_AS_PATHNAME);
    }

    public function current()
    {
        $data = file_get_contents($this->iterator->current());

        return $this->encoder->decode($data);
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function next()
    {
    return $this->iterator->next();
    }

    public function rewind()
    {
        return $this->iterator->rewind();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

    public function setIterator(\Iterator $iterator)
    {
        $this->iterator = $iterator;
    }
}
