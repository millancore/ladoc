<?php

namespace Ladoc\Tests\Unit\Action;

use Ladoc\Action\ListAction;
use Ladoc\Index\IndexList;
use Ladoc\Index\IndexManager;
use Ladoc\Index\ItemList;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Action\ListAction
 *
 * @uses \Ladoc\Index\IndexList
 * @uses \Ladoc\Index\ItemList
 * @uses \Ladoc\Index\Render
 *
 */
class ListActionTest extends TestCase
{
    public function test_it_can_return_main_list_as_html(): void
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


    public function test_it_can_return_main_list_filtered(): void
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
        $this->assertEquals('<p class="title">Main List | filter: V</p><ul><li>[0] Validation (validation)</li></ul>', $html);
    }

}
