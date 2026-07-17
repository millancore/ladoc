<?php

declare(strict_types=1);

namespace Ladoc\Mcp;

use InvalidArgumentException;
use stdClass;

class Server
{
    private const LATEST_PROTOCOL_VERSION = '2025-06-18';

    private const SUPPORTED_PROTOCOL_VERSIONS = [
        '2024-11-05',
        '2025-03-26',
        self::LATEST_PROTOCOL_VERSION,
    ];

    public function __construct(
        private readonly ToolHandler $toolHandler,
        private readonly string $version
    ) {
        //
    }

    /**
     * @codeCoverageIgnore
     */
    public function run(): void
    {
        while (($line = fgets(STDIN)) !== false) {

            if (trim($line) === '') {
                continue;
            }

            $response = $this->handle($line);

            if ($response !== null) {
                fwrite(STDOUT, json_encode($response) . "\n");
            }
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function handle(string $json): ?array
    {
        $request = json_decode($json, true);

        if (!is_array($request)) {
            return $this->error(null, -32700, 'Parse error');
        }

        if (!isset($request['id'])) {
            return null;
        }

        $id = $request['id'];
        $params = (array) ($request['params'] ?? []);

        return match ($request['method'] ?? '') {
            'initialize' => $this->result($id, $this->initialize($params)),
            'ping' => $this->result($id, new stdClass()),
            'tools/list' => $this->result($id, ['tools' => $this->toolHandler->definitions()]),
            'tools/call' => $this->callTool($id, $params),
            default => $this->error($id, -32601, sprintf('Method %s not found', $request['method'] ?? '')),
        };
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function initialize(array $params): array
    {
        $protocolVersion = $params['protocolVersion'] ?? '';

        if (!in_array($protocolVersion, self::SUPPORTED_PROTOCOL_VERSIONS, true)) {
            $protocolVersion = self::LATEST_PROTOCOL_VERSION;
        }

        return [
            'protocolVersion' => $protocolVersion,
            'capabilities' => ['tools' => new stdClass()],
            'serverInfo' => [
                'name' => 'ladoc',
                'version' => $this->version,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function callTool(mixed $id, array $params): array
    {
        try {
            $result = $this->toolHandler->call(
                (string) ($params['name'] ?? ''),
                (array) ($params['arguments'] ?? [])
            );
        } catch (InvalidArgumentException $exception) {
            return $this->error($id, -32602, $exception->getMessage());
        }

        return $this->result($id, [
            'content' => [
                ['type' => 'text', 'text' => $result['text']]
            ],
            'isError' => $result['isError'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function result(mixed $id, mixed $result): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function error(mixed $id, int $code, string $message): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];
    }

}
