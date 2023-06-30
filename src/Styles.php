<?php

namespace Lo;

class Styles
{

    public string $titleStyles;
    public string $inlineCodeStyles;

    public function __construct(array $styles)
    {
        $this->titleStyles = $styles['title'];
        $this->inlineCodeStyles = $styles['inline-code'];
    }


}