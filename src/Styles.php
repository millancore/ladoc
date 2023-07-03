<?php

namespace Lo;

class Styles
{
    public string $title;
    public string $inlineCode;

    public function __construct(array $styles)
    {
        $this->title = $styles['title'];
        $this->inlineCode = $styles['inline-code'];
    }


}
