<?php

declare(strict_types=1);

namespace Ladoc;

use Ladoc\Action\ActionInterface;
use Ladoc\Exception\FileManagerException;
use Ladoc\Index\IndexManager;
use Ladoc\Process\ProcessFactory;

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
     * @param array<string, mixed> $options
     * @return ActionInterface
     * @throws FileManagerException
     */
    public function resolve(
        string|int $section,
        array      $query = [],
        array      $options = []
    ): ActionInterface {
        if (is_numeric($section)) {
            $section = $this->resolveNumericalSection((int) $section, $options);
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

        return new Action\SectionQueryAction(
            $this->indexManager,
            new ProcessFactory(),
            $section
        );
    }


    /**
     * @param int $section
     * @param array<string, mixed> $option
     * @return string
     * @throws FileManagerException
     */
    private function resolveNumericalSection(int $section, array $option = []): string
    {
        $mainList = $this->indexManager->getMainIndex();

        if (isset($option['letter'])) {
            $mainList = $this->indexManager->getMainIndex()->filterByLetter($option['letter']);
        }

        return $mainList->get($section)->anchor;
    }


    /**
     * @param array<string|int> $query
     * @return bool
     */
    private function queryHasOnlyNumber(array $query): bool
    {
        if (empty($query)) {
            return false;
        }

        return !in_array(false, array_map(fn ($item) => is_numeric($item), $query));
    }


}
