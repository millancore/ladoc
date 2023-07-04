<?php

namespace Lo\Formatter;

readonly class TermwindFormatter implements FormatterInterface
{
    public function removeStyleBlocks(string $html): string
    {
        return preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
    }

    public function setTitleStyles(string $styles, string $html): string
    {
        $el = sprintf('<p class="%s">$1</p>', $styles);

        return preg_replace('/<h\d[^>]*>(.*?)<\/h\d>/i', $el, $html);
    }

    public function setInlineCodeStyles(string $styles, string $html): string
    {
        $el = sprintf('<span class="%s">$1</span>', $styles);

        return preg_replace('/<code[^>]*>(.*?)<\/code>/i', $el, $html);
    }

    public function removePreTags(string $html): string
    {
        return str_replace(['<pre>','</pre>'], '', $html);
    }

    public function removeConflictClasse(string $html): string
    {
        return  str_replace([
            'content-list',
            'collection-method-list'
        ], '', $html);
    }

    public function format(string $html): string
    {
        $html = $this->removeStyleBlocks($html);
        $html = $this->setTitleStyles('title', $html);
        $html = $this->setInlineCodeStyles('inline-code', $html);
        $html = $this->removeConflictClasse($html);
        $html = $this->removePreTags($html);

        return $html;
    }

}
