<?php

namespace Ladoc\Action;

interface ActionInterface
{
    /**
     * @param array<string, string> $query
     * @param array<string, string> $options
     * @return string
     */
    public function execute(array $query, array $options = []): string;
}
