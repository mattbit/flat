<?php

namespace Mattbit\Flat\Model;

interface DocumentInterface
{
    /**
     * Access a document attribute using dot notation.
     *
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * Check if a document attribute is set using dot notation.
     *
     * @param $key
     * @return mixed
     */
    public function has($key);

    /**
     * Return the document identifier.
     *
     * @return string
     */
    public function getId();

    /**
     * Set the document identifier.
     *
     * @param $id string
     */
    public function setId($id);
}
