<?php

declare(strict_types=1);

namespace Ladoc\Action;

use Ladoc\Index\IndexManager;
use Ladoc\Process\ProcessFactory;

readonly class SectionQueryAction implements ActionInterface
{
    public function __construct(
        private IndexManager $indexManager,
        private ProcessFactory $processFactory,
        private string $section
    ) {
        //
    }

    /**
     * @param array<int|string, string> $query
     * @param array<string, mixed> $options
     * @return string
     */
    public function execute(array $query, array $options = []): string
    {
        $sectionPath = $this->indexManager->getSectionPath($this->section);

        $process = $this->processFactory->newProcess(['grep',
            '-rl',
            $sectionPath,
            '--include=*.html',
            '-ie',
            implode(' ', $query)
        ]);

        $process->run();

        $output = explode("\n", $process->getOutput());
        $output = array_filter($output);

        $output = array_reverse($output);

        $content = '';
        foreach ($output as $file) {
            $content .= file_get_contents($file);
        }

        return $content;
    }
}
