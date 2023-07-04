<?php

namespace Lo\Action;

use Lo\Index\IndexManager;
use Lo\Index\Render;

class ListAction implements ActionInterface
{
    public function __construct(
        private IndexManager $indexManager
    ) {
    }


    public function execute(array $query, array $options = []): string
    {
        $mainList = $this->indexManager->getMainIndex();

        return Render::mainIndexList($mainList);
    }
}
