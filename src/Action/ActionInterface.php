<?php

namespace Lo\Action;

interface ActionInterface
{
    public function execute(array $query, array $options = []): string;
}
