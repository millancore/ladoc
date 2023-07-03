<?php

namespace Lo\Action;

use Lo\Index\IndexManager;

class SectionIndexAction implements ActionInterface
{
    public function __construct(
        private IndexManager $indexManager,
        private string $section
    ) {
        //
    }

    public function execute(array $query, array $options = []): string
    {
        // TODO: Implement execute() method.
    }
}
