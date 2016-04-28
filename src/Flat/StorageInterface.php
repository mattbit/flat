<?php

namespace Mattbit\Flat;

interface StorageInterface
{
    public function find(array $criteria);

    public function remove(array $criteria, $multiple = false);

    public function update(array $criteria, array $updates, $multiple = false);
}
