<?php

declare(strict_types=1);

namespace Lo\Action;

use Lo\Index\IndexList;
use Lo\Index\IndexManager;
use Lo\Index\Render;

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
