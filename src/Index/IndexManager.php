<?php

namespace Lo\Index;

use Exception;
use League\CommonMark\CommonMarkConverter;
use Lo\Enum\Version;
use Lo\FileManager;
use Lo\Formatter\TermwindFormatter;
use Lo\Repository;
use Lo\Section;
use Lo\Splitter;

class IndexManager
{
    /**
     * @throws Exception
     */
    public function __construct(
        private readonly Version $version,
        private readonly FileManager $fileManager
    ) {
        //
    }

    public function check(): bool
    {
        return true;
    }

    public function createIndex(Repository $repository): void
    {
        if(!$repository->check()) {
            $repository->download();
        }

        $files = $this->fileManager->getFolderFiles(
            $this->fileManager->docsPath .'/'. $this->version->value,
            ['.git', 'documentation.md']
        );

        $mainIndex = new IndexList();

        foreach ($files as $file) {
            $this->createIndexByFile($file, $mainIndex);
        }

        $this->saveMainIndex($mainIndex);
    }

    public function getMainIndex(): IndexList
    {
        return unserialize(file_get_contents($this->getIndexPath() . '/index'));
    }

    public function saveMainIndex(IndexList $indexList): void
    {
        $this->fileManager->save(
            $this->getIndexPath() . '/index',
            serialize($indexList)
        );
    }

    /**
     * @throws Exception
     */
    public function getSectionIndex(string $section): IndexList
    {
        if(!$this->sectionIndexFileExist($section)) {
            throw new Exception('Index section file not found');
        }

        return unserialize(file_get_contents($this->getIndexPath() . '/' . $section . '/index'));
    }


    public function getIndexPath(): string
    {
        return INDEX_DIR . '/' . $this->version->value;
    }

    public function mainIndexFileExist(): bool
    {
        return $this->fileManager->fileExist($this->getIndexPath() . '/index');
    }

    public function sectionIndexFileExist(string $section): bool
    {
        return $this->fileManager->fileExist($this->getIndexPath() . '/' . $section . '/index');
    }

    private function createIndexByFile(string $file, IndexList $mainIndex): void
    {
        $markdownContent = $this->fileManager->getFileContent($file);

        $section = pathinfo($file, PATHINFO_FILENAME);

        $html = (new CommonMarkConverter())->convert($markdownContent);

        $splitter = new Splitter($section, $html);

        if ($splitter->getTitle()) {
            $mainIndex->attach($splitter->getTitle());
        }

        $section = new Section(
            $section,
            $splitter->getIndexList(),
            $splitter->splitArticles(new TermwindFormatter())
        );

        $this->saveSection($section);
    }

    private function saveSection(Section $section): void
    {
        $sectionPath = $this->getIndexPath() . '/' . $section->name;

        foreach ($section->articles as $name => $content) {

            $filename = strtolower($name) . '.html';

            $this->fileManager->save($sectionPath. '/' . $filename, $content);
        }

        if (!$section->indexList->isEmpty()) {
            $this->fileManager->save($sectionPath. '/index', serialize($section->indexList));
        }
    }

    public function getArticle(string $section, string $anchor): string
    {
        return $this->fileManager->getFileContent(
            $this->getIndexPath() . '/' . $section . '/' . $anchor . '.html'
        );
    }

}
