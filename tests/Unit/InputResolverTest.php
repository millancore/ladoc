<?php

namespace Lo\Tests\Unit;

use Lo\Action;
use Lo\Index\IndexList;
use Lo\Index\IndexManager;
use Lo\Index\ItemList;
use Lo\InputResolver;

/**
 * @covers \Lo\InputResolver
 * @covers \Lo\Action\ListAction
 * @covers \Lo\Action\SectionIndexAction
 * @covers \Lo\Action\SectionListAction
 * @covers \Lo\Action\SectionQueryAction
 */
class InputResolverTest extends TestCase
{
    public function test_it_can_get_main_list_action(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $inputResolver = new InputResolver($indexManager);

        $action = $inputResolver->resolve('list');

        $this->assertInstanceOf(Action\ListAction::class, $action);
    }


    public function test_it_can_get_main_list_letter_filter(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $inputResolver = new InputResolver($indexManager);

        $action = $inputResolver->resolve('list', ['letter' => 'a']);

        $this->assertInstanceOf(Action\ListAction::class, $action);
    }

    /**
     * @covers \Lo\Index\IndexList
     * @covers \Lo\Index\ItemList
     */
    public function test_it_can_get_section_by_index(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $mainIndex = new IndexList('Main List');
        $mainIndex->attach(new ItemList('Artisan', 'artisan', new IndexList()));

        $indexManager
            ->method('getMainIndex')
            ->willReturn($mainIndex);

        $inputResolver = new InputResolver($indexManager);

        $action = $inputResolver->resolve(0);

        $this->assertInstanceOf(Action\SectionListAction::class, $action);
    }

    public function test_it_can_get_section_by_name(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $inputResolver = new InputResolver($indexManager);

        $action = $inputResolver->resolve('artisan');

        $this->assertInstanceOf(Action\SectionListAction::class, $action);
    }

    public function test_it_can_search_section(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $inputResolver = new InputResolver($indexManager);

        $action = $inputResolver->resolve('validation', ['routes']);

        $this->assertInstanceOf(Action\SectionQueryAction::class, $action);
    }


}
