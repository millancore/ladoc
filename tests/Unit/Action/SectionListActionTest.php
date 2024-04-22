<?php

namespace Ladoc\Tests\Unit\Action;

use Ladoc\Action\SectionListAction;
use Ladoc\Index\IndexList;
use Ladoc\Index\IndexManager;
use Ladoc\Index\ItemList;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Action\SectionListAction
 *
 * @uses \Ladoc\Index\IndexList
 * @uses \Ladoc\Index\ItemList
 * @uses \Ladoc\Index\Render
 */
class SectionListActionTest extends TestCase
{
    public function test_it_can_return_section_list_as_html(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $sectionList = new IndexList('Artisan');
        $sectionList->attach(new ItemList('Introduction', 'artisan-intro', new IndexList()));
        $sectionList->attach(
            new ItemList(
                'Commands',
                'artisan-commands',
                (new IndexList())->attach(new ItemList('Make Command', 'artisan-make-command'))
            )
        );

        $indexManager->method('getSectionIndex')
            ->willReturn($sectionList);

        $listAction = new SectionListAction($indexManager, 'artisan');

        $html = $listAction->execute([], []);

        $this->assertIsString($html);
        $this->assertEquals('<p class="title">Artisan</p><ul><li>[0] Introduction </li><li>[1] Commands (+)</li></ul>', $html);
    }

}
