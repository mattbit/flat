<?php

namespace Mattbit\Flat\Storage;

use Mattbit\Flat\Storage\EncoderInterface;
use Mattbit\Flat\Storage\EngineInterface;
use Mattbit\Flat\Storage\DocumentStore;

class FilesystemEngine implements EngineInterface
{
    /**
     * @var EncoderInterface
     */
    protected $encoder;

    protected $namespace;

    public function __construct($path, EncoderInterface $encoder)
    {
        $this->namespace = $path;
        $this->encoder = $encoder;
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

    public function put($document, $id, $collection)
    {
        $data = $this->encoder->encode($document);

        return file_put_contents($this->path($id, $collection), $data) !== false;
    }

    public function get($id, $collection)
    {
        $data = file_get_contents($this->path($id, $collection));

        return $this->encoder->decode($data);
    }

    public function delete($id, $collection)
    {
        return unlink($this->path($id, $collection));
    }

    public function all($collection)
    {
        return new FilesystemIterator($this->encoder, $this->path('*', $collection));
    }

    public function clear($collection)
    {
        return array_map('unlink', glob($this->path('*', $collection)));
    }

    public function init($collection)
    {
        $dir = $this->path('', $collection, '');

        if (!file_exists($dir)) {
            return mkdir($this->path('', $collection, ''));
        }

        return true;
    }

    public function has($id, $collection)
    {
        return file_exists($this->path($id, $collection));
    }

    public function destroy($collection)
    {
        array_map('unlink', glob($this->path('*', $collection, '')));
        return rmdir($this->path('', $collection, ''));
    }

    protected function path($id, $collection, $ext = '.data')
    {
        return $this->namespace.DIRECTORY_SEPARATOR.$collection.DIRECTORY_SEPARATOR.$id.$ext;
    }
}
