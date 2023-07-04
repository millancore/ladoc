<?php

namespace Lo\Index;

class Render
{
    public static function mainIndexList(IndexList $indexList): string
    {
        $html = '<ul>';
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
        $html = '<ul>';
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
