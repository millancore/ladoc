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
        $searchTerm = implode(' ', $query);

        $files = $this->grepFiles(
            $this->indexManager->getSectionPath($this->section),
            $searchTerm
        );

        if (empty($files)) {
            return $this->renderNoResults($searchTerm);
        }

        $files = array_reverse($files);

        $content = '';
        foreach ($files as $file) {
            $content .= file_get_contents($file);
        }

        return $content;
    }

    /**
     * @return array<string>
     */
    private function grepFiles(string $path, string $searchTerm): array
    {
        $process = $this->processFactory->newProcess(['grep',
            '-rl',
            $path,
            '--include=*.html',
            '-ie',
            $searchTerm
        ]);

        $process->run();

        return array_filter(explode("\n", $process->getOutput()));
    }

    private function renderNoResults(string $searchTerm): string
    {
        $searchTerm = htmlspecialchars($searchTerm);

        $message = sprintf(
            '<p>No results for "%s" in section "%s".</p>',
            $searchTerm,
            $this->section
        );

        $otherSections = $this->sectionsWithMatches($searchTerm);

        if (empty($otherSections)) {
            return $message . '<p>No other section contains this term.</p>';
        }

        return $message . sprintf(
            '<p>Sections with matches: %s</p><p>Try: ladoc %s %s</p>',
            implode(', ', $otherSections),
            $otherSections[0],
            $searchTerm
        );
    }

    /**
     * @return array<string>
     */
    private function sectionsWithMatches(string $searchTerm): array
    {
        $files = $this->grepFiles($this->indexManager->getIndexPath(), $searchTerm);

        $sections = array_map(fn ($file) => basename(dirname($file)), $files);
        $sections = array_unique(array_diff($sections, [$this->section]));
        sort($sections);

        return array_values($sections);
    }
}
