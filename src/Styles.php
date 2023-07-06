<?php

namespace Lo;

readonly class Styles
{
    /**
     * @param array<string, string> $styles
     */
    public function __construct(public array $styles)
    {
        //
    }

    public function get(string $name): string
    {
        return $this->styles[$name];
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->styles;
    }

}
