<?php

namespace Mattbit\Flat\Storage;

use Mattbit\Flat\Document\Encodable;

interface EncoderInterface
{
    public function encode(Encodable $document);
    public function decode($data);
    public function getExtension();
}