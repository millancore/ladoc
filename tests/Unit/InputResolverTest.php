<?php

namespace Ladoc\Tests\Unit;

use Ladoc\Action;
use Ladoc\Index\IndexList;
use Ladoc\Index\IndexManager;
use Ladoc\Index\ItemList;
use Ladoc\InputResolver;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\InputResolver
 */
class InputResolverTest extends TestCase
{
    /**
     * @uses \Ladoc\Action\ListAction
     */
    public function test_it_can_get_main_list_action(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $inputResolver = new InputResolver($indexManager);

        $action = $inputResolver->resolve('list');

        $this->assertInstanceOf(Action\ListAction::class, $action);
    }


    /**
     * @uses \Ladoc\Action\ListAction
     */
    public function test_it_can_get_main_list_letter_filter(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $inputResolver = new InputResolver($indexManager);

        $action = $inputResolver->resolve('list', ['letter' => 'a']);

        $this->assertInstanceOf(Action\ListAction::class, $action);
    }

    /**
     * @uses \Ladoc\Action\SectionListAction
     * @uses  \Ladoc\Index\IndexList
     * @uses  \Ladoc\Index\ItemList
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

    /**
     * @uses \Ladoc\Action\SectionListAction
     */
    public function test_it_can_get_section_by_name(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $inputResolver = new InputResolver($indexManager);

        $action = $inputResolver->resolve('artisan');

        $this->assertInstanceOf(Action\SectionListAction::class, $action);
    }

    /**
     * @uses \Ladoc\Action\SectionQueryAction
     */
    public function test_it_can_search_section(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $inputResolver = new InputResolver($indexManager);

        $action = $inputResolver->resolve('validation', ['routes']);

        $this->assertInstanceOf(Action\SectionQueryAction::class, $action);
    }


}
