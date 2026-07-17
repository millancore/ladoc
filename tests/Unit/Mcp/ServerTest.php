<?php

namespace Ladoc\Tests\Unit\Mcp;

use Ladoc\Mcp\Server;
use Ladoc\Mcp\ToolHandler;
use Ladoc\Tests\TestCase;

/**
 * @covers \Ladoc\Mcp\Server
 */
class ServerTest extends TestCase
{
    public function test_it_can_initialize(): void
    {
        $server = $this->getServer();

        $response = $server->handle(json_encode([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => ['protocolVersion' => '2025-06-18'],
        ]));

        $this->assertSame(1, $response['id']);
        $this->assertSame('2025-06-18', $response['result']['protocolVersion']);
        $this->assertSame('ladoc', $response['result']['serverInfo']['name']);
    }

    public function test_it_falls_back_to_supported_protocol_version(): void
    {
        $server = $this->getServer();

        $response = $server->handle(json_encode([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => ['protocolVersion' => '1999-01-01'],
        ]));

        $this->assertSame('2025-06-18', $response['result']['protocolVersion']);
    }

    public function test_it_can_list_tools(): void
    {
        $toolHandler = $this->createMock(ToolHandler::class);
        $toolHandler->method('definitions')->willReturn([
            ['name' => 'search_docs']
        ]);

        $server = new Server($toolHandler, 'test-version');

        $response = $server->handle(json_encode([
            'jsonrpc' => '2.0',
            'id' => 2,
            'method' => 'tools/list',
        ]));

        $this->assertSame([['name' => 'search_docs']], $response['result']['tools']);
    }

    public function test_it_can_call_tool(): void
    {
        $toolHandler = $this->createMock(ToolHandler::class);
        $toolHandler
            ->method('call')
            ->with('search_docs', ['section' => 'blade', 'query' => '@once'])
            ->willReturn(['text' => 'The @once Directive', 'isError' => false]);

        $server = new Server($toolHandler, 'test-version');

        $response = $server->handle(json_encode([
            'jsonrpc' => '2.0',
            'id' => 3,
            'method' => 'tools/call',
            'params' => [
                'name' => 'search_docs',
                'arguments' => ['section' => 'blade', 'query' => '@once'],
            ],
        ]));

        $this->assertFalse($response['result']['isError']);
        $this->assertSame('The @once Directive', $response['result']['content'][0]['text']);
    }

    public function test_error_on_invalid_tool_arguments(): void
    {
        $toolHandler = $this->createMock(ToolHandler::class);
        $toolHandler
            ->method('call')
            ->willThrowException(new \InvalidArgumentException('Unknown tool foo'));

        $server = new Server($toolHandler, 'test-version');

        $response = $server->handle(json_encode([
            'jsonrpc' => '2.0',
            'id' => 4,
            'method' => 'tools/call',
            'params' => ['name' => 'foo'],
        ]));

        $this->assertSame(-32602, $response['error']['code']);
        $this->assertSame('Unknown tool foo', $response['error']['message']);
    }

    public function test_error_on_unknown_method(): void
    {
        $response = $this->getServer()->handle(json_encode([
            'jsonrpc' => '2.0',
            'id' => 5,
            'method' => 'resources/list',
        ]));

        $this->assertSame(-32601, $response['error']['code']);
    }

    public function test_it_ignores_notifications(): void
    {
        $response = $this->getServer()->handle(json_encode([
            'jsonrpc' => '2.0',
            'method' => 'notifications/initialized',
        ]));

        $this->assertNull($response);
    }

    public function test_error_on_invalid_json(): void
    {
        $response = $this->getServer()->handle('{invalid');

        $this->assertSame(-32700, $response['error']['code']);
    }

    private function getServer(): Server
    {
        return new Server(
            $this->createMock(ToolHandler::class),
            'test-version'
        );
    }

}
