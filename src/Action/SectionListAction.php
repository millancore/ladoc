<?php

namespace Lo\Action;

use Lo\Index\IndexManager;
use Lo\Index\Render;

readonly class SectionListAction implements ActionInterface
{
    public function __construct(
        private IndexManager $indexManager,
        private string       $section
    ) {
        //
    }

    public function execute(array $query, array $options = []): string
    {
        $indexSection = $this->indexManager->getIndexSection($this->section);

        return Render::sectionIndexList($indexSection);
    }
}
