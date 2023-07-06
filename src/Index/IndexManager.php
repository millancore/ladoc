<?php

namespace Lo\Index;

use Exception;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use Lo\Exception\FileManagerException;
use Lo\FileManager;
use Lo\Formatter\TermwindFormatter;
use Lo\Section;
use Lo\Splitter;

class IndexManager
{
    private string $indexFileName = 'index.ladoc';

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly FileManager $fileManager
    ) {
        //
    }

    public function check(): bool
    {
        return $this->indexFolderExist() && $this->mainIndexFileExist();
    }

    /**
     * @throws FileManagerException
     * @throws CommonMarkException
     */
    public function createIndex(): void
    {
        $files = $this->fileManager->getRepositoryFiles(['.git', 'documentation.md']);

        $mainIndex = new IndexList('Main List');

        foreach ($files as $file) {
            $this->createIndexByFile($file, $mainIndex);
        }

        $this->saveMainIndex($mainIndex);
    }

    /**
     * @throws FileManagerException
     */
    public function getMainIndex(): IndexList
    {
        return unserialize($this->fileManager->getFileContent($this->indexFileName));
    }

    /**
     * @throws FileManagerException
     */
    public function saveMainIndex(IndexList $indexList): void
    {
        $this->fileManager->saveIndexFile(
            $this->indexFileName,
            serialize($indexList)
        );
    }

    /**
     * @throws Exception
     */
    public function getSectionIndex(string $section): IndexList
    {
        if (!$this->sectionIndexFileExist($section)) {
            throw new Exception('Index section file not found');
        }

        return unserialize($this->fileManager->getFileContent(
            $section . '/' . $this->indexFileName
        ));
    }


    public function indexFolderExist(): bool
    {
        return is_dir($this->fileManager->getIndexPath());
    }

    public function mainIndexFileExist(): bool
    {
        return file_exists($this->fileManager->getIndexPath() . '/' . $this->indexFileName);
    }


    public function sectionIndexFileExist(string $section): bool
    {
        return file_exists($this->fileManager->getIndexPath() . '/' . $section . '/' . $this->indexFileName);
    }

    /**
     * @throws FileManagerException
     * @throws CommonMarkException
     */
    private function createIndexByFile(string $file, IndexList $mainIndex): void
    {
        $markdownContent = file_get_contents($file);

        if($markdownContent === false) {
            throw new FileManagerException(sprintf('File %s is empty', $file));
        }

        $section = pathinfo($file, PATHINFO_FILENAME);

        $html = (new CommonMarkConverter())->convert($markdownContent);
        $splitter = new Splitter($html);

        if ($splitter->getTitle()) {
            $mainIndex->attach(new ItemList($splitter->getTitle(), $section));
        }

        $section = new Section(
            $section,
            $splitter->getIndexList(),
            $splitter->splitArticles(new TermwindFormatter())
        );

        $this->saveSection($section);
    }

    /**
     * @throws FileManagerException
     */
    private function saveSection(Section $section): void
    {
        foreach ($section->articles as $name => $content) {

            $filename = strtolower($name) . '.html';

            $this->fileManager->saveIndexFile(
                $section->name . '/' . $filename,
                $content
            );
        }

        if (!$section->indexList->isEmpty()) {
            $this->fileManager->saveIndexFile($section->name. '/' . $this->indexFileName, serialize($section->indexList));
        }
    }

    /**
     * @throws FileManagerException
     */
    public function getArticle(string $section, string $anchor): string
    {
        return $this->fileManager->getFileContent($section . '/' . $anchor . '.html');
    }

}
