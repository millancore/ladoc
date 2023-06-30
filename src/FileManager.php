<?php

namespace Lo;

class FileManager
{
    public function __construct(
        private Version $version,
        private Settings $settings,
    )
    {
        //
    }

    public function versionDocumentFolderExist() : bool
    {
        return is_dir($this->getVersionDocumentPath());
    }

    public function getVersionDocumentPath(): string
    {
        return $this->settings->docPath . '/' . $this->version->value;
    }

    public function getVersionFiles() : array
    {
        $path = $this->getVersionDocumentPath();

        $files = array_filter(scandir($path), fn ($file) => !in_array($file, ['.', '..']));

        return array_map(fn ($file) => $path . '/' . $file, $files);
    }

    /**
     * @throws \Exception
     */
    public function saveIndexSection(string $section, string $filename, string $content) : void
    {
        $path = sprintf(
            '%s/%s/%s/%s',
            $this->settings->indexPath,
            $this->version->value,
            $section,
            $filename
        );

        $this->save($path, $content);
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