<?php

namespace Lo;


use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;

readonly class Indexer
{

    public function __construct(
        private FileManager         $fileManager,
        private CommonMarkConverter $converter
    )
    {
        //
    }

    /**
     * @throws CommonMarkException
     */
    public function createIndexForVersion()
    {
        // validate if version doc folder exist
        if (!$this->fileManager->versionDocumentFolderExist()) {
            // download version for repo
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
    public function createIndexByFile(string $file) : void
    {
        $markdownContent = $this->fileManager->getFileContent($file);

        $section = pathinfo($file, PATHINFO_FILENAME);

        $html = $this->converter->convert($markdownContent);

        $html = str_replace([
            'content-list',
            'collection-method-list'
        ], '', $html);

        $formatter = new TermwindFormatter(
            new Styles([
                    'title' => 'bg-teal-500',
                    'inline-code' => 'bg-gray-500',
                ]
            )
        );

        $html = $formatter->format($html);

        $splitter = new Splitter($html);

        $splitHTML = $splitter->splitSections();

        $this->saveIndexSection($section, $splitHTML);

    }


    /**
     * @throws \Exception
     */
    public function saveIndexSection(string $section, array $articles) : void
    {
        foreach ($articles as $name => $content) {
            $filename = strtolower($name) . '.html';

            $this->fileManager->saveIndexSection(
                $section,
                $filename,
                $content
            );
        }
    }

}