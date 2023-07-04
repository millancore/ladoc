<?php

namespace Lo;

class FileManager
{
    public function __construct(
        public readonly string $docsPath,
        public readonly string $indexPath
    ) {
        //
    }

    public function folderExist(string $path): bool
    {
        return is_dir($path);
    }

    public function fileExist(string $path): bool
    {
        return is_file($path);
    }

    /**
     * @param string $path
     * @param array $exclude
     * @return array [string]
     */
    public function getFolderFiles(string $path, array $exclude = []): array
    {
        $excludeFiles = ['.', '..'];

        if (!$this->folderExist($path)) {
            return [];
        }

        $exclude = array_merge($exclude, $excludeFiles);

        $files = array_filter(scandir($path), fn ($file) => !in_array($file, $exclude));

        return array_map(fn ($file) => $path . '/' . $file, $files);
    }

    /**
     * @throws \Exception
     */
    public function save(string $filename, string $content): void
    {
        $successCreateDir = $this->createDirectory(
            str_replace('/' . basename($filename), '', $filename)
        );

        if (!$successCreateDir) {
            throw new \Exception('Error creating directory');

        }

        file_put_contents($filename, $content);
    }


    public function getFileContent(string $filePath): string
    {
        return file_get_contents($filePath);
    }


    private function createDirectory(string $path): bool
    {
        if (!is_dir($path)) {
            return mkdir($path, 0777, true);
        }

        return true;
    }

}
