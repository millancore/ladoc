<?php

declare(strict_types=1);

namespace Ladoc;

use FilesystemIterator;
use Ladoc\Enum\Version;
use Ladoc\Exception\FileManagerException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

readonly class FileManager
{
    public function __construct(
        private Version $version,
        private string  $docsPath,
        private string  $indexPath
    ) {
        //
    }

    public function getDocPath(): string
    {
        return $this->docsPath . '/' . $this->version->value;
    }

    public function getIndexPath(): string
    {
        return $this->indexPath . '/' . $this->version->value;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    /**
     * @param array<string> $exclude
     * @return string[]
     * @throws FileManagerException
     */
    public function getRepositoryFiles(array $exclude = []): array
    {
        $path = $this->getDocPath();

        if (!is_dir($path)) {
            throw new FileManagerException(sprintf('Repository folder %s not found', $path));
        }

        return $this->getFolderFiles($path, $exclude);
    }


    /**
     * @param string $path
     * @param array<string> $exclude
     * @return array<string>
     */
    private function getFolderFiles(string $path, array $exclude = []): array
    {
        $excludeFiles = ['.', '..'];
        $arrayFiles = scandir($path);

        if ($arrayFiles === false) {
            return [];
        }

        $exclude = array_merge($exclude, $excludeFiles);
        $files = array_filter($arrayFiles, fn ($file) => !in_array($file, $exclude));

        return array_map(fn ($file) => $path . '/' . $file, $files);
    }

    /**
     * @param string $filename
     * @param string $content
     * @return void
     * @throws FileManagerException
     */
    private function save(string $filename, string $content): void
    {
        $successCreateDir = $this->createDirectory(dirname($filename));

        if (!$successCreateDir) {
            throw new FileManagerException('Error to create directory');

        }

        file_put_contents($filename, $content);
    }


    /**
     * @return void
     */
    public function removeIndexDirectory(): void
    {
        if (!is_dir($this->getIndexPath())) {
            return;
        }

        $this->removeDirectory($this->getIndexPath());
    }

    /**
     * @param string $filename
     * @return string
     * @throws FileManagerException
     */
    public function getFileContent(string $filename): string
    {
        $path = $this->getIndexPath() . '/' . $filename;

        if (!file_exists($path)) {
            throw new FileManagerException(sprintf('File %s not found', $path));
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new FileManagerException(sprintf('Error to read file %s', $path));
        }

        return $content;
    }


    /**
     * @param string $filename
     * @param string $content
     * @return void
     * @throws FileManagerException
     */
    public function saveIndexFile(string $filename, string $content): void
    {
        $path = $this->getIndexPath() . '/' . $filename;
        $this->save($path, $content);
    }


    /**
     * @param string $path
     * @return void
     */
    private function removeDirectory(string $path): void
    {
        $directoryIterator = new RecursiveDirectoryIterator(
            $path,
            FilesystemIterator::SKIP_DOTS
        );

        $iterator = new RecursiveIteratorIterator(
            $directoryIterator,
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }

        rmdir($path);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function createDirectory(string $path): bool
    {
        if (!is_dir($path)) {
            return mkdir($path, 0777, true);
        }

        return true;
    }

}
