<?php

namespace Lo\Formatter;

interface FormatterInterface
{
    public function format(string $html): string;
}
