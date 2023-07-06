<?php

namespace Ladoc\Tests\Unit\Index;

use Ladoc\Index\IndexList;
use Ladoc\Index\ItemList;
use Ladoc\Index\Render;
use Ladoc\Tests\Unit\TestCase;

/**
 * @covers \Ladoc\Index\Render
 * @uses  \Ladoc\Index\IndexList
 * @uses  \Ladoc\Index\ItemList
 */
class RenderTest extends TestCase
{
    public function test_it_can_render_main_list(): void
    {
        $indexList = new IndexList('Test List');

        $indexList->attach(new ItemList('title one', 'anchor-one'));
        $indexList->attach(new ItemList('title two', 'anchor-two'));
        $indexList->attach(new ItemList('title three', 'anchor-three'));

        $this->assertSame(
            '<p class="title">Test List</p><ul><li>[0] title one (anchor-one)</li><li>[1] title two (anchor-two)</li><li>[2] title three (anchor-three)</li></ul>',
            Render::mainIndexList($indexList)
        );
    }

    public function test_it_can_render_section_list(): void
    {
        $indexList = new IndexList('Test Section List');

        $indexList->attach(new ItemList('title one', 'anchor-one'));
        $indexList->attach(
            new ItemList(
                'title two',
                'anchor-two',
                (new IndexList())
                    ->attach(new ItemList('child title', 'anchor'))
            )
        );
        $indexList->attach(new ItemList('title three', 'anchor-three'));

        $this->assertSame(
            '<p class="title">Test Section List</p><ul><li>[0] title one </li><li>[1] title two (+)</li><li>[2] title three </li></ul>',
            Render::sectionIndexList($indexList)
        );
    }

}
