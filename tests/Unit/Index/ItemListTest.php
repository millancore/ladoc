<?php

namespace Lo\Tests\Unit\Index;

use Lo\Index\IndexList;
use Lo\Index\ItemList;
use Lo\Tests\Unit\TestCase;

/**
 * @covers \Lo\Index\ItemList
 * @covers \Lo\Index\IndexList
 */
class ItemListTest extends TestCase
{
    public function test_it_can_create_item_and_access_properties(): void
    {
        $item = new ItemList('title', 'anchor', new IndexList());

        $this->assertSame('title', $item->title);
        $this->assertSame('anchor', $item->anchor);
        $this->assertInstanceOf(IndexList::class, $item->children);
    }

    public function test_it_can_get_nested_item_as_array(): void
    {

        $indexList = new IndexList();
        $indexList->attach(new ItemList('child title', 'anchor', new IndexList()));

        $item = new ItemList('title', 'anchor', $indexList);

        $this->assertSame([
            'title' => 'title',
            'anchor' => 'anchor',
            'child' => [
                [
                    'title' => 'child title',
                    'anchor' => 'anchor',
                    'child' => []
                ]
            ]
        ], $item->toArray());
    }

}