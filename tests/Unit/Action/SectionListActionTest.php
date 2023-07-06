<?php

namespace Lo\Tests\Unit\Action;

use Lo\Action\SectionListAction;
use Lo\Index\IndexList;
use Lo\Index\IndexManager;
use Lo\Index\ItemList;
use Lo\Tests\Unit\TestCase;


/**
 * @covers \Lo\Action\SectionListAction
 *
 * @uses \Lo\Index\IndexList
 * @uses \Lo\Index\ItemList
 * @uses \Lo\Index\Render
 */
class SectionListActionTest extends TestCase
{

    public function test_it_can_return_section_list_as_html() : void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $sectionList = new IndexList('Artisan');
        $sectionList->attach(new ItemList('Introduction', 'artisan-intro', new IndexList()));
        $sectionList->attach(
            new ItemList(
                'Commands',
                'artisan-commands',
                (new IndexList())->attach(new ItemList('Make Command', 'artisan-make-command'))
            ));

        $indexManager->method('getSectionIndex')
            ->willReturn($sectionList);

        $listAction = new SectionListAction($indexManager, 'artisan');

        $html = $listAction->execute([], []);

        $this->assertIsString($html);
        $this->assertEquals('<p class="title">Artisan</p><ul><li>[0] Introduction </li><li>[1] Commands (+)</li></ul>', $html);
    }

}