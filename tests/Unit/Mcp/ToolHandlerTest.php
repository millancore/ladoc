<?php

namespace Ladoc\Tests\Unit\Mcp;

use Ladoc\Mcp\ToolHandler;
use Ladoc\Process\Process;
use Ladoc\Process\ProcessFactory;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Mcp\ToolHandler
 */
class ToolHandlerTest extends TestCase
{
    public function test_it_defines_all_tools(): void
    {
        $toolHandler = new ToolHandler('/path/bin/ladoc');

        $names = array_column($toolHandler->definitions(), 'name');

        $this->assertSame(['list_sections', 'get_section', 'search_docs'], $names);
    }

    public function test_it_can_call_search_docs(): void
    {
        $process = $this->createMock(Process::class);
        $process->method('run')->willReturn(0);
        $process->method('isSuccessful')->willReturn(true);
        $process->method('getOutput')->willReturn('The @once Directive');

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->expects($this->once())
            ->method('newProcess')
            ->with([PHP_BINARY, '/path/bin/ladoc', '-b', '10.x', 'blade', '@once'])
            ->willReturn($process);

        $toolHandler = new ToolHandler('/path/bin/ladoc', $processFactory);

        $result = $toolHandler->call('search_docs', [
            'section' => 'blade',
            'query' => '@once',
            'version' => '10.x',
        ]);

        $this->assertSame('The @once Directive', $result['text']);
        $this->assertFalse($result['isError']);
    }

    public function test_it_can_call_list_sections(): void
    {
        $process = $this->createMock(Process::class);
        $process->method('run')->willReturn(0);
        $process->method('isSuccessful')->willReturn(true);
        $process->method('getOutput')->willReturn('Main List');

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory
            ->method('newProcess')
            ->with([PHP_BINARY, '/path/bin/ladoc'])
            ->willReturn($process);

        $toolHandler = new ToolHandler('/path/bin/ladoc', $processFactory);

        $result = $toolHandler->call('list_sections', []);

        $this->assertSame('Main List', $result['text']);
    }

    public function test_it_reports_cli_failure_as_error(): void
    {
        $process = $this->createMock(Process::class);
        $process->method('run')->willReturn(1);
        $process->method('isSuccessful')->willReturn(false);
        $process->method('getOutput')->willReturn('Section "foo" not found.');
        $process->method('getErrorOutput')->willReturn('');

        $processFactory = $this->createMock(ProcessFactory::class);
        $processFactory->method('newProcess')->willReturn($process);

        $toolHandler = new ToolHandler('/path/bin/ladoc', $processFactory);

        $result = $toolHandler->call('get_section', ['section' => 'foo']);

        $this->assertTrue($result['isError']);
        $this->assertStringContainsString('Section "foo" not found.', $result['text']);
    }

    public function test_error_on_unknown_tool(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ToolHandler('/path/bin/ladoc'))->call('unknown_tool', []);
    }

    public function test_error_on_missing_required_argument(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required argument "query"');

        (new ToolHandler('/path/bin/ladoc'))->call('search_docs', ['section' => 'blade']);
    }

}
