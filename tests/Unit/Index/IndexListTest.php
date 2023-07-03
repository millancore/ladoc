<?php

namespace Lo\Tests\Unit\Index;

use Lo\Index\IndexList;
use Lo\Index\ItemList;
use Lo\Tests\Unit\TestCase;

class IndexListTest extends TestCase
{
    public function test_it_can_attach_item_list(): void
    {
        $indexList = new IndexList();
        $indexList->attach(new ItemList('title', 'anchor', new IndexList()));

        $this->assertEquals(1, $indexList->count());
    }

    public function test_it_can_get_nested_item_as_array() : void
    {
        $indexList = new IndexList();
        $indexList->attach(new ItemList('child title', 'anchor', new IndexList()));

        $this->assertSame([
            [
                'title' => 'child title',
                'anchor' => 'anchor',
                'child' => []
            ]
        ], $indexList->toArray());
    }

    public function test_it_can_get_nested_items() : void
    {
        $firstLevel = new IndexList();
        $secondLevel = new IndexList();
        $thirdLevel = new IndexList();

        $thirdLevel->attach(new ItemList('third level', 'anchor'));
        $secondLevel->attach(new ItemList('first child second level', 'anchor'));
        $secondLevel->attach(new ItemList('second child second level', 'anchor', $thirdLevel));
        $firstLevel->attach(new ItemList('first level', 'anchor', $secondLevel));


        $this->assertSame([
            'title' => 'third level',
            'anchor' => 'anchor',
            'child' => null
        ], $firstLevel->getNestedItems([0, 1, 0])->toArray());

    }

}