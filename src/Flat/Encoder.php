<?php

namespace Mattbit\Flat;

class Encoder
{
    const EXTENSION = 'json';

    public function encode(array $object)
    {
        return json_encode($object);
    }

    public function decode($data)
    {
        return json_decode($data, true);
    }
}
