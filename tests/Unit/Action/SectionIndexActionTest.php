<?php

namespace Ladoc\Tests\Unit\Action;

use Ladoc\Action\SectionIndexAction;
use Ladoc\Index\IndexManager;
use Ladoc\Tests\TestCase;

/**
 * @covers  \Ladoc\Action\SectionIndexAction
 *
 * @uses \Ladoc\Index\Render
 * @uses \Ladoc\Index\IndexList
 * @uses \Ladoc\Index\ItemList
 */
class SectionIndexActionTest extends TestCase
{
    public function test_it_can_return_with_nested_index(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $indexTest = file_get_contents(ROOT_TEST . '/data/artisan.ladoc');

        $indexManager->expects($this->once())
            ->method('getSectionIndex')
            ->with('artisan')
            ->willReturn(unserialize((string) $indexTest));

        $indexManager->expects($this->once())
            ->method('getArticle')
            ->with('artisan', 'input-arrays')
            ->willReturn('<h1>Input Arrays</h1>');

        $action = new SectionIndexAction($indexManager, 'artisan');

        $output = $action->execute([2, 2]);

        $this->assertStringContainsString('<h1>Input Arrays</h1>', $output);
    }

    public function test_it_can_return_with_child_list(): void
    {
        $indexManager = $this->createMock(IndexManager::class);

        $indexTest = file_get_contents(ROOT_TEST . '/data/artisan.ladoc');

        $indexManager->expects($this->once())
            ->method('getSectionIndex')
            ->with('artisan')
            ->willReturn(unserialize((string) $indexTest));

        $indexManager->expects($this->once())
            ->method('getArticle')
            ->with('artisan', 'defining-input-expectations')
            ->willReturn('<h1>Defining Input Expectations</h1>');

        $action = new SectionIndexAction($indexManager, 'artisan');

        $output = $action->execute([2]);

        $this->assertStringContainsString('<h1>Defining Input Expectations</h1>', $output);
        $this->assertStringContainsString('<ul><li>[0] Arguments </li><li>[1] Options </li><li>[2] Input Arrays </li><li>[3] Input Descriptions </li></ul>', $output);
    }

}
