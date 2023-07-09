<?php

namespace Ladoc\Action;

interface ActionInterface
{
    /**
     * @param array<int|string, int|string> $query
     * @param array<string, mixed> $options
     * @return string
     */
    public function execute(array $query, array $options = []): string;
}
