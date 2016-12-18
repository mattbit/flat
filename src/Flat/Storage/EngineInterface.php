<?php

namespace Mattbit\Flat\Storage;

interface EngineInterface
{
    public function put($data, $id, $namespace);

    public function get($id, $namespace);

    public function delete($id, $namespace);

    public function clear($namespace);

    public function init($namespace);

    public function has($id, $namespace);
}
