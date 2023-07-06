<?php

namespace Ladoc\Formatter;

interface FormatterInterface
{
    public function format(string $html): string;
}
