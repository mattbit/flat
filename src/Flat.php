<?php

namespace Mattbit;

use League\Flysystem\Plugin\ListPaths;
use Mattbit\Flat\Database;
use Mattbit\Flat\Storage\JsonEncoder;
use Mattbit\Flat\Storage\FilesystemEngine;

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
        $encoder = new JsonEncoder();
        $engine = new FilesystemEngine($path, $encoder);

        return new Database($engine);
    }
}
