<?php

declare(strict_types=1);

namespace Ladoc\Action;

use Ladoc\Index\IndexManager;
use Ladoc\Index\Render;

class ListAction implements ActionInterface
{
    public function __construct(
        private IndexManager $indexManager
    ) {
    }


    public function execute(array $query, array $options = []): string
    {
        $mainList = $this->indexManager->getMainIndex();

        if($options['letter']) {
            $mainList = $mainList->filterByLetter($options['letter']);
        }

        return Render::mainIndexList($mainList);
    }
}
