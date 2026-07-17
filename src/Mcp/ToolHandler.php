<?php

declare(strict_types=1);

namespace Ladoc\Mcp;

use InvalidArgumentException;
use Ladoc\Process\ProcessFactory;

class ToolHandler
{
    public function __construct(
        private readonly string $binPath,
        private readonly ProcessFactory $processFactory = new ProcessFactory()
    ) {
        //
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function definitions(): array
    {
        $versionSchema = [
            'type' => 'string',
            'description' => "Laravel version branch, e.g. '12.x' or '5.8'. Defaults to the latest version."
        ];

        return [
            [
                'name' => 'list_sections',
                'description' => 'List all sections of the Laravel documentation. '
                    . 'Section names are shown in parentheses, e.g. "Blade Templates (blade)".',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'version' => $versionSchema,
                    ],
                ],
            ],
            [
                'name' => 'get_section',
                'description' => 'Get the topic index of a documentation section, e.g. all topics of "blade".',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'section' => [
                            'type' => 'string',
                            'description' => 'Section name, e.g. "blade" or "eloquent-relationships".'
                        ],
                        'version' => $versionSchema,
                    ],
                    'required' => ['section'],
                ],
            ],
            [
                'name' => 'search_docs',
                'description' => 'Search a section of the Laravel documentation and return the matching articles. '
                    . 'If there are no matches, other sections containing the term are suggested.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'section' => [
                            'type' => 'string',
                            'description' => 'Section name, e.g. "blade" or "eloquent-relationships".'
                        ],
                        'query' => [
                            'type' => 'string',
                            'description' => 'Search term, e.g. "@once" or "hasMany".'
                        ],
                        'version' => $versionSchema,
                    ],
                    'required' => ['section', 'query'],
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $arguments
     * @return array{text: string, isError: bool}
     */
    public function call(string $name, array $arguments): array
    {
        $commandArguments = match ($name) {
            'list_sections' => [],
            'get_section' => [$this->requireString($arguments, 'section')],
            'search_docs' => [
                $this->requireString($arguments, 'section'),
                $this->requireString($arguments, 'query')
            ],
            default => throw new InvalidArgumentException(sprintf('Unknown tool %s', $name)),
        };

        if (isset($arguments['version'])) {
            $commandArguments = ['-b', (string) $arguments['version'], ...$commandArguments];
        }

        return $this->runCli($commandArguments);
    }

    /**
     * @param array<string, mixed> $arguments
     */
    private function requireString(array $arguments, string $key): string
    {
        if (empty($arguments[$key]) || !is_string($arguments[$key])) {
            throw new InvalidArgumentException(sprintf('Missing required argument "%s"', $key));
        }

        return $arguments[$key];
    }

    /**
     * @param array<string> $commandArguments
     * @return array{text: string, isError: bool}
     */
    private function runCli(array $commandArguments): array
    {
        $process = $this->processFactory->newProcess([
            PHP_BINARY,
            $this->binPath,
            ...$commandArguments
        ]);

        $process->run();

        $text = trim($process->getOutput());

        if (!$process->isSuccessful()) {
            $text = trim($text . "\n" . trim($process->getErrorOutput()));
        }

        return [
            'text' => $text,
            'isError' => !$process->isSuccessful(),
        ];
    }

}
