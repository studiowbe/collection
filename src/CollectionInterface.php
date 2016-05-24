<?php

namespace Studiow\Collection;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use ArrayAccess;

interface CollectionInterface extends ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{

    /**
     * 
     * @param mixed $items
     * @return \static
     */
    public static function create($items = []);

    /**
     * @return array
     */
    public function all();

    /**
     * 
     * @param callable $callback
     * @return \static
     */
    public function map(callable $callback);

    /**
     * 
     * @param callable $callback
     * @return \static
     */
    public function filter(callable $callback = null);

    /**
     * 
     * @param int $length
     * @param int $offset
     * @return \static
     */
    public function slice($length, $offset = 0);

    /**
     * @return int
     */
    public function count();
}
