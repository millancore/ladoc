<?php

namespace Lo;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use Lo\Formatter\FormatterInterface;
use Lo\Index\IndexList;

readonly class Indexer
{
    private IndexList $mainIndex;


    public function __construct(
        private FileManager         $fileManager,
        private FormatterInterface $formatter,
        private CommonMarkConverter $converter
    ) {
        $this->mainIndex = new IndexList();
    }

    /**
     * @throws CommonMarkException
     */
    public function createIndexForVersion(): void
    {
        // validate if version doc folder exist
        if (!$this->fileManager->versionDocumentFolderExist()) {
            // download version for repo
            //dump('exit');
            //die;
        }

        //read files for version folder
        $files = $this->fileManager->getVersionFiles();

        //create index files
        foreach ($files as $file) {
            $this->createIndexByFile($file);
        }
    }

    /**
     * @throws CommonMarkException
     * @throws \Exception
     */
    public function createIndexByFile(string $file): void
    {
        $markdownContent = $this->fileManager->getFileContent($file);

        $section = pathinfo($file, PATHINFO_FILENAME);

        $html = $this->converter->convert($markdownContent);

        $html = str_replace([
            'content-list',
            'collection-method-list'
        ], '', $html);

        $splitter = new Splitter($section, $html, $this->formatter);

        if ($splitter->getTitle()) {
            $this->mainIndex->attach($splitter->getTitle());
        }

        $sectionArticles = $splitter->splitArticles();

        $this->saveSectionIndex($section, $splitter);
        $this->saveSectionArticles($section, $sectionArticles);
    }


    public function getMainIndex(): IndexList
    {
        return $this->mainIndex;
    }


    /**
     * @throws \Exception
     */
    public function saveSectionIndex(string $section, Splitter $splitter)
    {
        $indexSection = $splitter->getIndexList();

        $this->fileManager->saveSectionIndex($section, $indexSection->getAsArrayFile());
    }


    /**
     * @throws \Exception
     */
    public function saveSectionArticles(string $section, array $articles): void
    {
        foreach ($articles as $name => $content) {
            $filename = strtolower($name) . '.html';

            $this->fileManager->saveSectionArticle($section, $filename, $content);
        }
    }

}
