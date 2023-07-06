<?php

declare(strict_types=1);

namespace Lo\Action;

use Lo\Index\IndexManager;
use Symfony\Component\Process\Process;

readonly class SectionQueryAction implements ActionInterface
{
    public function __construct(
        private IndexManager $indexManager,
        private string $section
    ) {
        //
    }

    public function execute(array $query, array $options = []): string
    {
        if(!$this->indexManager->sectionIndexFileExist($this->section)) {
            return '';
        }

        $sectionPath = $this->indexManager->getSectionPath($this->section);

        $process = new Process(['grep',
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
