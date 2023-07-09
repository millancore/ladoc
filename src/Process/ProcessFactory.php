<?php

namespace Ladoc\Process;

class ProcessFactory
{
    /**
     * @param array<string> $parameters
     * @return ProcessInterface
     */
    public function newProcess(array $parameters): ProcessInterface
    {
        return new Process($parameters);
    }

}
