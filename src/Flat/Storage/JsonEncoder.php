<?php

namespace Mattbit\Flat\Storage;

use Mattbit\Flat\Document\Document;
use Mattbit\Flat\Document\Encodable;

class JsonEncoder implements EncoderInterface
{
    public function encode(Encodable $document)
    {
        return json_encode($document->toArray());
    }

    public function decode($data)
    {
        return new Document(json_decode($data, true));
    }

    public function getExtension()
    {
        return 'json';
    }
}
