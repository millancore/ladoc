<?php

declare(strict_types=1);

namespace Ladoc\Action;

use Ladoc\Index\IndexManager;
use Ladoc\Index\Render;

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
        $indexSection = $this->indexManager->getSectionIndex($this->section);

        return Render::sectionIndexList($indexSection);
    }
}
