<?php

namespace Mattbit\Flat\Model;

use DateTime;
use JsonSerializable;

class Date extends DateTime implements JsonSerializable
{
    public function jsonSerialize()
    {
        return $this->format(self::ISO8601);
    }
}
