<?php namespace Mattbit\Flat\Document;

interface Encodable
{
    public function __construct(array $attributes);
    public function toArray();
}