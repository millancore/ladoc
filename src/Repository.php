<?php

namespace Lo;

use Lo\Enum\Version;
use Symfony\Component\Process\Process;

class Repository
{
    public const REPO_URL = 'https://github.com/laravel/docs.git';
    public function __construct(
        private readonly  Version $version,
        private readonly FileManager $fileManager
    ) {
        //
    }

    public function check(): bool
    {
        return true;
    }

    public function download(): bool
    {
        $command = [
            'git',
            'clone',
            '--branch',
            $this->version->value,
            self::REPO_URL,
            $this->fileManager->docsPath
        ];

        $process = new Process($command);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return true;
    }


}
