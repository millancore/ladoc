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

    public function test_it_reports_no_results_and_suggests_other_sections(): void
    {
        $indexManager = $this->createMock(IndexManager::class);
        $processFactory = $this->createMock(ProcessFactory::class);

        $indexManager->method('getSectionPath')->willReturn('/tmp/index/artisan');
        $indexManager->method('getIndexPath')->willReturn('/tmp/index');

        $process = $this->createMock(Process::class);
        $process->method('run')->willReturn(1);
        $process->method('getOutput')->willReturnOnConsecutiveCalls(
            '',
            "/tmp/index/validation/foo.html\n/tmp/index/queries/bar.html"
        );

        $processFactory->method('newProcess')->willReturn($process);

        $action = new SectionQueryAction($indexManager, $processFactory, 'artisan');

        $content = $action->execute(['hasMany']);

        $this->assertStringContainsString('No results for "hasMany" in section "artisan".', $content);
        $this->assertStringContainsString('Sections with matches: queries, validation', $content);
        $this->assertStringContainsString('Try: ladoc queries hasMany', $content);
    }

    public function test_it_reports_no_results_in_any_section(): void
    {
        $indexManager = $this->createMock(IndexManager::class);
        $processFactory = $this->createMock(ProcessFactory::class);

        $indexManager->method('getSectionPath')->willReturn('/tmp/index/artisan');
        $indexManager->method('getIndexPath')->willReturn('/tmp/index');

        $process = $this->createMock(Process::class);
        $process->method('run')->willReturn(1);
        $process->method('getOutput')->willReturn('');

        $processFactory->method('newProcess')->willReturn($process);

        $action = new SectionQueryAction($indexManager, $processFactory, 'artisan');

        $content = $action->execute(['nonexistent']);

        $this->assertStringContainsString('No results for "nonexistent" in section "artisan".', $content);
        $this->assertStringContainsString('No other section contains this term.', $content);
    }

}
