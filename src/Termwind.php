<?php

namespace Lo;

use function Termwind\render;
use function Termwind\style;

readonly class Termwind
{
    public function __construct(private Styles $styles)
    {
        //
    }


    public function loadStyles(): void
    {
        style('title')->apply($this->styles->title);
        style('inline-code')->apply($this->styles->inlineCode);
    }


    public function render(string $html): void
    {
        $this->loadStyles();
        render(sprintf('<div>%s</div>', $html));
    }

}
