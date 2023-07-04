<?php

namespace Lo;

/*
  *  $ list
  *  $ list -l=a (--letter )
  *  $ list ...index numbers
  *  $ <section>
  *  $ <section> ...index
  *  $ <section> ...query
  */

use Exception;
use Lo\Action\ActionInterface;
use Lo\Index\IndexManager;

class InputResolver
{
    public function __construct(
        private readonly IndexManager $indexManager
    ) {
        //
    }


    /**
     * @throws Exception
     */
    public function resolve(string $section, array $query, array $options = []): string
    {
        if (is_numeric($section)) {
            $section = $this->indexManager->getMainIndex()->get($section)->anchor;
        }

        $action = $this->actions($section, $query);
        return $action->execute($query, $options);
    }

    private function actions(string $section, array $query): ActionInterface
    {
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
