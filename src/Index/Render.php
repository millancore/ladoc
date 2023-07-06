<?php

declare(strict_types=1);

namespace Lo\Index;

class Render
{
    public static function mainIndexList(IndexList $indexList): string
    {
        $html = '';
        if($indexList->getName()) {
            $html .= sprintf('<p class="title">%s</p>', $indexList->getName());
        }

        $html .= '<ul>';
        foreach ($indexList->all() as $index => $item) {
            $html .= '<li>';
            $html .= sprintf('[%d] %s (%s)', $index, $item->title, $item->anchor);
            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    public static function sectionIndexList(IndexList $indexList): string
    {
        $html = '';
        if($indexList->getName()) {
            $html .= sprintf('<p class="title">%s</p>', $indexList->getName());
        }

        $html .= '<ul>';
        foreach ($indexList->all() as $index => $item) {
            $html .= '<li>';

            $children = '';
            if ($item->hasChildren()) {
                $children = '(+)';
            }

            $html .= sprintf('[%d] %s %s', $index, $item->title, $children);
            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }

}
