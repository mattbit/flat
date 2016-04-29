<?php

namespace Mattbit;

use Mattbit\Flat\Database;
use Mattbit\Flat\Query\Parser;
use Mattbit\Flat\Storage\JsonEncoder;
use Mattbit\Flat\Storage\Engine;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

class Flat
{
    /**
     * Select the database.
     *
     * @param  string $path
     *
     * @return Database
     */
    public static function localDatabase($path)
    {
        $adapter = new Local($path);
        $filesystem = new Filesystem($adapter);
        $encoder = new JsonEncoder();
        $engine = new Engine($filesystem, $encoder);

        return new Database($engine);
    }
}
