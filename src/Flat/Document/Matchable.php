<?php

namespace Mattbit\Flat\Document;

interface Matchable
{
    public function get($key);
    public function has($key);
}
