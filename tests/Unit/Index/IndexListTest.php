<?php

namespace Ladoc\Tests\Unit\Index;

use Ladoc\Index\IndexList;
use Ladoc\Index\ItemList;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Index\IndexList
 * @uses  \Ladoc\Index\ItemList
 */
class IndexListTest extends TestCase
{
    public function test_it_can_attach_item_list(): void
    {
        $indexList = new IndexList();
        $indexList->attach(new ItemList('title', 'anchor', new IndexList()));

        $this->assertEquals(1, $indexList->count());
    }

    public function test_it_can_set_name(): void
    {
        $indexList = new IndexList();
        $indexList->setName('name');

        $this->assertSame('name', $indexList->getName());
    }

    public function test_it_can_get_by_index(): void
    {
        $indexList = new IndexList();
        $indexList->attach(new ItemList('first title', 'first anchor'));
        $indexList->attach(new ItemList('second title', 'second anchor'));

        $this->assertSame('first title', $indexList->get(0)->title);
    }

    public function test_it_can_get_all_items(): void
    {
        $indexList = new IndexList();
        $indexList->attach(new ItemList('title', 'anchor'));
        $indexList->attach(new ItemList('title', 'anchor'));
        $indexList->attach(new ItemList('title', 'anchor'));

        $this->assertCount(3, $indexList->all());
    }

    public function test_it_can_validate_if_empty(): void
    {
        $indexList = new IndexList();
        $this->assertTrue($indexList->isEmpty());
    }

    public function test_it_can_filter_by_first_letter(): void
    {
        $indexList = new IndexList();
        $indexList->attach(new ItemList('title', 'anchor', new IndexList()));
        $indexList->attach(new ItemList('title', 'anchor', new IndexList()));
        $indexList->attach(new ItemList('title', 'anchor', new IndexList()));

        $this->assertCount(3, $indexList->all());
        $this->assertCount(0, $indexList->filterByLetter('t'));
        $this->assertCount(3, $indexList->filterByLetter('a'));
    }

    public function test_it_can_get_nested_item_as_array(): void
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

    public function test_it_can_get_nested_items(): void
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
