<?php

namespace Mattbit\Flat\Document;

interface DocumentInterface extends \ArrayAccess
{
    public function toArray();
}
