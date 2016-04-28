<?php

namespace Mattbit;

use Mattbit\Flat\Config;
use Mattbit\Flat\Database;

class Flat
{
    /**
     * Select the database.
     *
     * @param  $name
     * @param Config $config
     *
     * @return Database
     */
    public static function database($name, Config $config)
    {
        return new Database($name, $config);
    }
}
