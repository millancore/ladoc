<?php

declare(strict_types=1);

namespace Lo;

use Lo\Action\ActionInterface;
use Lo\Exception\FileManagerException;
use Lo\Index\IndexManager;

class InputResolver
{
    public function __construct(
        private readonly IndexManager $indexManager
    ) {
        //
    }

    /**
     * @param string|int $section
     * @param array<string|int> $query
     * @return ActionInterface
     * @throws FileManagerException
     */
    public function resolve(string|int $section, array $query = []): ActionInterface
    {
        if (is_numeric($section)) {
            $section = $this->indexManager->getMainIndex()->get(
                $section
            )->anchor;
        }

        if ($section === 'list') {
            return new Action\ListAction($this->indexManager);
        }

        if (empty($query)) {
            return new Action\SectionListAction($this->indexManager, $section);
        }

        if ($this->queryHasOnlyNumber($query)) {
            return new Action\SectionIndexAction($this->indexManager, $section);
        }

        return new Action\SectionQueryAction($this->indexManager, $section);
    }


    private function queryHasOnlyNumber(array $query): bool
    {
        if (empty($query)) {
            return false;
        }

        return !in_array(false, array_map(fn ($item) => is_numeric($item), $query));
    }


}
