<?php

namespace Lo\Action;

use Lo\Index\IndexManager;

class SectionQueryAction implements ActionInterface
{
    public function __construct(
        IndexManager $indexManager,
        string $section
    ) {
        //
    }

    public function execute(array $query, array $options = []): string
    {
        // TODO: Implement execute() method.
    }
}
