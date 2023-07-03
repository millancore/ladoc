<?php

namespace Lo\Tests\Unit\Index;

use Lo\Index\IndexList;
use Lo\Index\ItemList;
use Lo\Index\Render;
use Lo\Tests\Unit\TestCase;

class RenderTest extends TestCase
{
    public function test_it_can_render_main_list() : void
    {
        $indexList = new IndexList();

        $indexList->attach(new ItemList('title one', 'anchor-one'));
        $indexList->attach(new ItemList('title two', 'anchor-two'));
        $indexList->attach(new ItemList('title three', 'anchor-three'));

        $this->assertSame(
            '<ul><li>[0] title one <anchor-one></li><li>[1] title two <anchor-two></li><li>[2] title three <anchor-three></li></ul>',
            Render::mainIndexList($indexList)
        );
    }

    public function test_it_can_render_section_list() : void
    {
        $indexList = new IndexList();

        $indexList->attach(new ItemList('title one', 'anchor-one'));
        $indexList->attach(
            new ItemList(
                'title two',
                'anchor-two',
                (new IndexList())
                    ->attach(new ItemList('child title', 'anchor'))
            ));
        $indexList->attach(new ItemList('title three', 'anchor-three'));

        $this->assertSame(
            '<ul><li>[0] title one </li><li>[1] title two (+)</li><li>[2] title three </li></ul>',
            Render::sectionIndexList($indexList)
        );
    }

}