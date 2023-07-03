<?php

namespace Lo;

use Lo\Enum\Version;

class FileManager
{
    public function __construct(
        private Version $version,
        private Settings $settings,
    ) {
        //
    }

    public function versionDocumentFolderExist(): bool
    {
        return is_dir($this->getVersionDocumentPath());
    }

    public function versionIndexFolderExist(): bool
    {
        return is_dir($this->getVersionIndexPath());
    }

    private function getVersionIndexPath(): string
    {
        return $this->settings->indexPath . '/' . $this->version->value;
    }

    public function sectionExist(string $section): bool
    {
        return is_dir($this->getVersionIndexPath() . '/' . $section);
    }

    public function getVersionDocumentPath(): string
    {
        return $this->settings->docPath . '/' . $this->version->value;
    }

    public function getVersionFiles(): array
    {
        $path = $this->getVersionDocumentPath();

        $files = array_filter(scandir($path), fn ($file) => !in_array($file, [
            '.',
            '..',
            '.git',
            'index.php'
        ]));

        return array_map(fn ($file) => $path . '/' . $file, $files);
    }

    /**
     * @throws \Exception
     */
    public function saveSectionArticle(string $article, string $filename, string $content): void
    {
        $path = sprintf(
            '%s/%s/%s/%s',
            $this->settings->indexPath,
            $this->version->value,
            $article,
            $filename
        );

        $this->save($path, $content);
    }

    /**
     * @throws \Exception
     */
    public function saveSectionIndex(string $section, string $indexAsArrayFile): void
    {
        $path = sprintf(
            '%s/%s/%s/%s',
            $this->settings->indexPath,
            $this->version->value,
            $section,
            $section.'.php'
        );

        $this->save($path, $indexAsArrayFile);
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
