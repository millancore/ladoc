<?php

namespace Lo\Action;

class EmptyAction implements ActionInterface
{
    public function execute(array $query, array $options = []): string
    {
        return '';
    }
}
