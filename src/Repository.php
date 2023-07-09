<?php

declare(strict_types=1);

namespace Ladoc;

use Ladoc\Process\ProcessFactory;
use RuntimeException;

class Repository
{
    private const REPO_URL = 'https://github.com/laravel/docs.git';

    public function __construct(
        private readonly FileManager    $fileManager,
        private readonly ProcessFactory $processFactory
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

    private function download(): void
    {
        $command = [
            'git',
            'clone',
            '--branch',
            $this->fileManager->getVersion()->value,
            self::REPO_URL,
            $this->fileManager->getDocPath(),
        ];

        $process = $this->processFactory->newProcess($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }

    }


}
