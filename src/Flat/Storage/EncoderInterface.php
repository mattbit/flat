<?php

namespace Mattbit\Flat\Storage;

use Mattbit\Flat\Model\DocumentInterface;

interface EncoderInterface
{
    public function encode(DocumentInterface $document);
    public function decode($data);
}
