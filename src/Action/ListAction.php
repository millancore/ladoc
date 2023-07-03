<?php

namespace Lo\Action;

use Lo\Index\IndexManager;

class ListAction implements ActionInterface
{
    public function __construct(
        private IndexManager $indexManager
    ) {
    }


    public function execute(array $query, array $options = []): string
    {

    }
}
