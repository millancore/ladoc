<?php

namespace Ladoc\Tests\Unit\Action;

use Ladoc\Action\SectionQueryAction;
use Ladoc\Index\IndexManager;
use Ladoc\Process\Process;
use Ladoc\Process\ProcessFactory;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Action\SectionQueryAction
 */
class SectionQueryActionTest extends TestCase
{
    public function test_it_can_search_into_section_files(): void
    {
        $indexManager = $this->createMock(IndexManager::class);
        $processFactory = $this->createMock(ProcessFactory::class);

        $indexManager->method('getSectionPath')->willReturn('/tmp');

        $process = $this->createMock(Process::class);
        $process->method('run')->willReturn(1);
        $process->method('getOutput')->willReturn(
            ROOT_TEST. '/data/search/option-shortcuts.html'
        );

        $processFactory->method('newProcess')->willReturn($process);

        $action = new SectionQueryAction($indexManager, $processFactory, 'section');

        $content = $action->execute(['option', 'shortcuts']);
        $this->assertStringContainsString('<p class="title">Option Shortcuts</p>', $content);
    }

}
