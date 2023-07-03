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
use Lo\Action\EmptyAction;
use Lo\Index\IndexManager;
use Lo\Index\Render;

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
        $action = new EmptyAction();

        if($section === 'list') {
            $action = new Action\ListAction($this->indexManager);
        }

        if (empty($query)) {
            $action = new Action\SectionListAction($this->indexManager, $section);
        }

        if ($this->queryHasOnlyNumber($query)) {
            $action = new Action\SectionIndexAction($this->indexManager, $section);
        }

        if ($action instanceof EmptyAction) {
            $action = new Action\SectionQueryAction($this->indexManager, $section);
        }

        return $action->execute($query, $options);
    }


    /**
     * @throws Exception
     */
    public function section(string $section, array $query)
    {
        // check if section is a valid section
        $indexSection = $this->indexManager->getIndexSection($section);

        $isNumericQuery = $this->queryHasOnlyNumber($query);

        if ($isNumericQuery) {
            $itemList = $indexSection->getNestedItems($query);

            return Render::sectionIndexList($itemList);
        }




    }

    private function queryHasOnlyNumber(array $query): bool
    {
        return !in_array(false, array_map(fn ($item) => is_numeric($item), $query));
    }






}
