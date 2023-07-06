<?php

namespace Ladoc\Tests\Unit\Index;

use Ladoc\Index\IndexList;
use Ladoc\Index\ItemList;
use Ladoc\Tests\Unit\TestCase;

/**
 * @covers \Ladoc\Index\ItemList
 * @uses  \Ladoc\Index\IndexList
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

    public function test_it_can_validate_if_has_children(): void
    {
        $item = new ItemList(
            'title',
            'anchor',
            (new IndexList())->attach(new ItemList('child title', 'anchor'))
        );

        $this->assertTrue($item->hasChildren());
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
