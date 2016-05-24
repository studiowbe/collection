<?php

namespace Studiow\Collection\Test;

use Studiow\Collection\Collection;
use PHPUnit_Framework_TestCase;

class CollectionTest extends PHPUnit_Framework_TestCase
{

    public function testConvertToArray()
    {
        $collection = new Collection(['a', 'b', 'c']);
        $this->assertEquals(['a', 'b', 'c'], $collection->all());
    }

    public function testCount()
    {
        $collection = new Collection(['a', 'b', 'c']);
        $this->assertEquals(3, $collection->count());
    }

    public function testMap()
    {
        $collection = new Collection(['a', 'b', 'c']);
        $uc_collection = $collection->map("strtoupper");
        $this->assertEquals(['A', 'B', 'C'], $uc_collection->all());

        $append_collection = $collection->map(function($item) {
            return $item .= '_append';
        });
        $this->assertEquals(['a_append', 'b_append', 'c_append'], $append_collection->all());
    }

    public function testFilter()
    {
        $collection = new Collection(range(0, 5));
        $filtered = $collection->filter(function($item) {
            return $item < 2;
        });
        $this->assertEquals([0, 1], $filtered->all());
    }

    public function testSlice()
    {
        $collection = new Collection(range(0, 5));
        $sliced = $collection->slice(2);
        $this->assertEquals([0, 1], $sliced->all());
        $sliced_offset = $collection->slice(2, 1);

        $this->assertEquals([1, 2], $sliced_offset->all());
    }

    public function testNumPages()
    {
        $collection = new Collection(range(0, 10));
        $this->assertEquals(3, $collection->numPages(5));
        $this->assertEquals(1, $collection->numPages(20));
    }

    public function testPage()
    {
        $collection = new Collection(range(0, 10));
        $page_1 = $collection->page(0, 5);
        $page_2 = $collection->page(1, 5);
        $page_3 = $collection->page(2, 5);
        $page_4 = $collection->page(3, 5);
        $this->assertEquals([0, 1, 2, 3, 4], $page_1->all());
        $this->assertEquals([5, 6, 7, 8, 9], $page_2->all());
        $this->assertEquals([10], $page_3->all());
        $this->assertEquals([], $page_4->all());
    }

    public function testSort()
    {
        $collection = new Collection(['x', 'z', 'y']);
        $default_sort = $collection->sort(null);
        $this->assertEquals(['x', 'y', 'z'], $default_sort->all());
    }

    public function testSortOn()
    {
        $collection = new Collection(['a' => ['num' => 2], 'b' => ['num' => 1], 'c' => ['num' => 0]]);
        $asc = $collection->sortOn('num', SORT_ASC);
        $this->assertEquals(['c', 'b', 'a'], array_keys($asc->all()));
        $desc = $collection->sortOn('num', SORT_DESC);
        $this->assertEquals([ 'a', 'b', 'c'], array_keys($desc->all()));
    }

}
