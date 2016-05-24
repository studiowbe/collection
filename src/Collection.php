<?php

namespace Studiow\Collection;

use ArrayIterator;
use LogicException;

class Collection implements CollectionInterface
{

    protected $items = [];

    public function __construct($items = [])
    {
        $this->items = $this->convertToArray($items);
    }

    public static function create($items = array())
    {
        return new static($items);
    }

    public function all()
    {
        return $this->items;
    }

    public function count()
    {
        return sizeof($this->items);
    }

    public function filter(callable $callback = null)
    {
        return static::create(array_filter($this->items, $callback));
    }

    public function sort(callable $callback = null)
    {
        $items = $this->items;
        if (is_null($callback)) {
            sort($items);
        } else {
            usort($items, $callback);
        }
        return static::create($items);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    public function jsonSerialize()
    {
        return array_map([$this, 'convertForJson'], $this->items);
    }

    public function map(callable $callback)
    {
        return static::create(array_map($callback, $this->items));
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new LogicException("Collection is immutable");
    }

    public function offsetUnset($offset)
    {
        throw new LogicException("Collection is immutable");
    }

    public function slice($length, $offset = 0)
    {
        return static::create(array_slice($this->items, $offset, $length));
    }

    public function page($num, $perPage = 5)
    {
        return $this->slice($perPage, $num * $perPage);
    }

    public function numPages($perPage = 5)
    {
        return ceil($this->count() / $perPage);
    }

    protected function convertToArray($items)
    {
        if (is_object($items)) {
            if ($items instanceof CollectionInterface) {
                return $items->all();
            } else if (is_callable([$items, 'toArray'])) {
                return call_user_func([$items, 'toArray']);
            } else if ($items instanceOf JsonSerializable) {
                return $items->jsonSerialize();
            }
        }
        return (array) $items;
    }

    protected function convertForJson($item)
    {
        if (is_object($item)) {
            if ($item instanceOf JsonSerializable) {
                return $item->jsonSerialize();
            } else if (is_callable([$item, 'toJson'])) {
                return json_decode(call_user_func([$item, 'toJson']));
            }
        }
        return $item;
    }

    protected function readItemField($item, $field)
    {

        if (is_object($item)) {
            if (property_exists($item, $field) || is_callable([$item, '__get'])) {
                return $item->$field;
            }
        }
        if (!is_array($item)) {
            $item = $this->convertToArray($item);
        }
        return array_key_exists($field, $item) ? $item[$field] : null;
    }

    public function sortOn($field, $order = SORT_ASC)
    {

        $callback = function($a, $b) use($field, $order) {
            $val_a = $this->readItemField($a, $field);
            $val_b = $this->readItemField($b, $field);
            if ($val_a == $val_b) {
                $rv = 0;
            } else {
                $rv = $val_a > $val_b ? 1 : -1;
            }
            return $order === SORT_DESC ? -1 * $rv : $rv;
        };

        return $this->sort($callback);
    }

}
