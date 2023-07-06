<?php

declare(strict_types=1);

namespace Lo;

use Symfony\Component\Process\Process;

class Repository
{
    private const REPO_URL = 'https://github.com/laravel/docs.git';

    public function __construct(
        private readonly FileManager $fileManager
    ) {
        //
    }

    public function check(): void
    {
        $exist = is_dir($this->getDir());

        if (!$exist) {
            $this->createVersionDirectory();
            $this->download();
        }
    }

    private function getDir(): string
    {
        return $this->fileManager->getDocPath();
    }


    private function createVersionDirectory(): void
    {
        $this->fileManager->createDirectory($this->getDir());
    }

    public function download(): bool
    {
        $command = [
            'git',
            'clone',
            '--branch',
            $this->fileManager->getVersion()->value,
            self::REPO_URL,
            $this->fileManager->getDocPath(),
        ];

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return true;
    }


}
