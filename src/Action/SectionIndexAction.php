<?php

declare(strict_types=1);

namespace Ladoc\Action;

use Ladoc\Exception\FileManagerException;
use Ladoc\Index\IndexList;
use Ladoc\Index\IndexManager;
use Ladoc\Index\Render;

class SectionIndexAction implements ActionInterface
{
    public function __construct(
        private IndexManager $indexManager,
        private string $section
    ) {
        //
    }

    /**
     * @param array<int, int> $query
     * @param array<string, mixed> $options
     * @return string
     * @throws FileManagerException
     */
    public function execute(array $query, array $options = []): string
    {
        $section = $this->indexManager->getSectionIndex($this->section);

        $element = $section->getNestedItems(
            array_map(fn ($item) => (int) $item, $query)
        );

        $list = Render::sectionIndexList($element->children ?? new IndexList());
        $article = $this->indexManager->getArticle($this->section, $element->anchor);

        $output = $article;

        if ($list !== '') {
            $output .= '<hr>' . $list;
        }

        return $output;
    }
}
