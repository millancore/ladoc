<?php

namespace Lo\Tests\Unit\Action;

use Lo\Index\IndexList;
use Lo\Index\IndexManager;
use Lo\Index\ItemList;
use Lo\Tests\Unit\TestCase;

use Lo\Action\ListAction;

/**
 * @covers \Lo\Action\ListAction
 *
 * @uses \Lo\Index\IndexList
 * @uses \Lo\Index\ItemList
 * @uses \Lo\Index\Render
 *
 */
class ListActionTest extends TestCase
{
    public function test_it_can_return_main_list_as_html() : void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $mainList = new IndexList('Main List');
        $mainList->attach(new ItemList('Artisan Console', 'artisan'));
        $mainList->attach(new ItemList('Validation', 'validation'));

        $indexManager->method('getMainIndex')
            ->willReturn($mainList);

        $listAction = new ListAction($indexManager);

        $html = $listAction->execute([], []);

        $this->assertIsString($html);
        $this->assertEquals('<p class="title">Main List</p><ul><li>[0] Artisan Console (artisan)</li><li>[1] Validation (validation)</li></ul>', $html);
    }


    public function test_it_can_return_main_list_filtered() : void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $mainList = new IndexList('Main List');
        $mainList->attach(new ItemList('Artisan Console', 'artisan'));
        $mainList->attach(new ItemList('Validation', 'validation'));

        $indexManager->method('getMainIndex')
            ->willReturn($mainList);

        $listAction = new ListAction($indexManager);

        $html = $listAction->execute([], ['letter' => 'v']);

        $this->assertIsString($html);
        $this->assertEquals('<p class="title">Main List | filter: V</p><ul><li>[1] Validation (validation)</li></ul>', $html);
    }

}