<?php

namespace Ladoc\Process;

interface ProcessInterface
{
    /**
     * @param callable|null $callback
     * @param array<string> $env
     * @return int
     */
    public function run(callable $callback = null, array $env = []): int;

    public function getOutput(): string;

    public function isSuccessful(): bool;

    public function getErrorOutput(): string;
}
